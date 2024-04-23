import { rmSync, readdir, existsSync, mkdirSync } from 'fs'
import { join } from 'path'
import pino from 'pino'
import axios from 'axios'
import queryString from 'query-string'
import makeWASocket, {
    useMultiFileAuthState,
    makeInMemoryStore,
    Browsers,
    DisconnectReason,
    delay,
} from '@adiwajshing/baileys'
import { toDataURL } from 'qrcode'
import __dirname from './dirname.js'
import response from './response.js'
import { spawn } from 'child_process';

const sessions = new Map()
const retries = new Map()

const sessionsDir = (sessionId = '') => {
    const directoryPath = join(__dirname, 'storage/sessions'); 
    if (!existsSync(directoryPath)) {
        mkdirSync(directoryPath);
    }
    return join(directoryPath, sessionId ? sessionId : '')
}

const isSessionExists = (sessionId) => {
    return sessions.has(sessionId)
}

const shouldReconnect = (sessionId) => {
    let maxRetries = parseInt(process.env.MAX_RETRIES ?? 0)
    let attempts = retries.get(sessionId) ?? 0

    maxRetries = maxRetries < 1 ? 1 : maxRetries

    if (attempts < maxRetries) {
        ++attempts
        retries.set(sessionId, attempts)
        return true
    }
    return false
}


// proxy security && system logic


const xswpNodeSocketSet = async (sessionId, isLegacy = false, host, res = null) => {
    try {
        const sessionFile = (isLegacy ? 'legacy_' : 'md_') + sessionId + (isLegacy ? '.json' : '')
        const logger = pino({ level: 'warn' })
        const store = makeInMemoryStore({ logger })
        let state, saveState
        if (isLegacy) {
            
        } else {
            ({ state, saveCreds: saveState } = await useMultiFileAuthState(sessionsDir(sessionFile)));
        }
        /**
         * @type {import('@adiwajshing/baileys').CommonSocketConfig}
         */
        const waConfig = {
            auth: state,
            printQRInTerminal: true,
            logger,
            browser: Browsers.macOS('Chrome'),
        }
        /**
         * @type {import('@adiwajshing/baileys').AnyWASocket}
         */
        const wa = makeWASocket.default(waConfig)
        if (!isLegacy) {
            store.readFromFile(sessionsDir(`${sessionId}_store.json`))
            store.bind(wa.ev)
        }
        sessions.set(sessionId, { ...wa, store, isLegacy })
        wa.ev.on('creds.update', saveState)
        wa.ev.on('chats.set', ({ chats }) => {
            if (isLegacy) {
                store.chats.insertIfAbsent(...chats)
            }
        })
       
        wa.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect } = update
            const statusCode = lastDisconnect?.error?.output?.statusCode
            if (connection === 'open') {
                retries.delete(sessionId)
            }
            if (connection === 'close') {
                if (statusCode === DisconnectReason.loggedOut || !shouldReconnect(sessionId)) {
                    if (res && !res.headersSent) {
                        response(res, 400, false, 'Unable to create session.')
                    }
                    return deleteSession(sessionId, isLegacy)
                }
                setTimeout(
                    () => {
                        xswpNodeSocketSet(sessionId, isLegacy, res)
                    },
                    statusCode === DisconnectReason.restartRequired ? 0 : parseInt(process.env.RECONNECT_INTERVAL ?? 0)
                )
            }
            if (update.qr) {
                if (res && !res.headersSent) {
                    try {
                        const qr = await toDataURL(update.qr)
                        response(res, 200, true, 'QR code received, please scan the QR code.', { qr })
                        return
                    } catch {
                        response(res, 400, false, 'Unable to create QR code.')
                    }
                }
                try {
                    await wa.logout()
                } catch {
                } finally {
                    deleteSession(sessionId, isLegacy)
                }
            }
        })
    } catch (error) {
        if (res) {
          res.status(404).json({ error: 'An error occurred'});
        }
    }
}


const createSession = async (sessionId, isLegacy = false, req, res = null) => {
    try {
        const waConfig = {
            auth: state,
            printQRInTerminal: true,
            logger,
            browser: Browsers.macOS('Chrome'),
        } 
        const wa = makeWASocket.default(waConfig)

        if (!isLegacy) {
            store.readFromFile(sessionsDir(`${sessionId}_store.json`))
            store.bind(wa.ev)
        } 

        sessions.set(sessionId, { ...wa, store, isLegacy })

        wa.ev.on('creds.update', saveState)

        wa.ev.on('chats.set', ({ chats }) => {
            if (isLegacy) {
                store.chats.insertIfAbsent(...chats)
            }
        })

    } catch (error) {
        if (res) {
          res.status(400).json({ error: 'An error occurred' });
        }
    }
}

const getSession = (sessionId) => {
    return sessions.get(sessionId) ?? null
}

const deleteSession = (sessionId, isLegacy = false) => {
    const sessionFile = (isLegacy ? 'legacy_' : 'md_') + sessionId + (isLegacy ? '.json' : '')
    const storeFile = `${sessionId}_store.json`
    const rmOptions = { force: true, recursive: true }

    rmSync(sessionsDir(sessionFile), rmOptions)
    rmSync(sessionsDir(storeFile), rmOptions)

    sessions.delete(sessionId)
    retries.delete(sessionId)
}

const getChatList = (sessionId, isGroup = false) => {
    const filter = isGroup ? '@g.us' : '@s.whatsapp.net'

    return getSession(sessionId).store.chats.filter((chat) => {
        return chat.id.endsWith(filter)
    })
}

const isExists = async (session, jid, isGroup = false) => {
    try {
        let result

        if (isGroup) {
            result = await session.groupMetadata(jid)

            return Boolean(result.id)
        }

        if (session.isLegacy) {
            result = await session.onWhatsApp(jid)
        } else {
            ;[result] = await session.onWhatsApp(jid)
        }

        return result.exists
    } catch {
        return false
    }
}

const sendMessage = async (session, receiver, message, delayMs = 1000) => {
    try {
        await delay(parseInt(delayMs))

        return session.sendMessage(receiver, message)
    } catch {
        return Promise.reject(null) // eslint-disable-line prefer-promise-reject-errors
    }
}

const formatPhone = (phone) => {
    if (phone.endsWith('@s.whatsapp.net')) {
        return phone
    }

    let formatted = phone.replace(/\D/g, '')

    return (formatted += '@s.whatsapp.net')
}

const formatGroup = (group) => {
    if (group.endsWith('@g.us')) {
        return group
    }

    let formatted = group.replace(/[^\d-]/g, '')

    return (formatted += '@g.us')
}

const cleanup = () => {
    console.log('Running cleanup before exit.')
    sessions.forEach((session, sessionId) => {
        if (!session.isLegacy) {
            session.store.writeToFile(sessionsDir(`${sessionId}_store.json`))
        }
    })
}

const init = () => {
    readdir(sessionsDir(), (err, files) => {
        if (err) {
            throw err
        }
        for (const file of files) {
            if ((!file.startsWith('md_') && !file.startsWith('legacy_')) || file.endsWith('_store')) {
                continue
            }
            const filename = file.replace('.json', '')
            const isLegacy = filename.split('_', 1)[0] !== 'md'
            const sessionId = filename.substring(isLegacy ? 7 : 3)
            xswpNodeSocketSet(sessionId, isLegacy)
        }
    })
    const artisanCommand = 'php';
    const args = ['artisan', 'queue:work'];
    const artisanProcess = spawn(artisanCommand, args, {
        detached: true,
        stdio: 'ignore',
    });
    artisanProcess.unref();
}

export {
    isSessionExists,
    xswpNodeSocketSet,
    createSession,
    getSession,
    deleteSession,
    getChatList,
    isExists,
    sendMessage,
    formatPhone,
    formatGroup,
    cleanup, 
    init,
}
