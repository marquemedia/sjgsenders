<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportFile;
use App\Models\SupportMessage;
use App\Http\Utility\SendMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $title = "Manage Support ticket";
        $supportTickets = SupportTicket::latest()->with('user')->paginate(paginateNumber());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    /**
     * @return View
     */
    public function running(): View
    {
        $title = "Manage running support ticket";
        $supportTickets = SupportTicket::where('status', 1)->with('user')->latest()->paginate(paginateNumber());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    /**
     * @return View
     */
    public function answered(): View
    {
        $title = "Manage answered support ticket";
        $supportTickets = SupportTicket::where('status', 2)->with('user')->latest()->paginate(paginateNumber());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    /**
     * @return View
     */
    public function replied(): View
    {
        $title = "Manage replied support ticket";
        $supportTickets = SupportTicket::where('status', 3)->with('user')->latest()->paginate(paginateNumber());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }


    /**
     * @return View
     */
    public function closed(): View
    {
        $title = "Manage closed support ticket";
        $supportTickets = SupportTicket::where('status', 4)->latest()->paginate(paginateNumber());
        return view('admin.support_ticket.index', compact('title', 'supportTickets'));
    }

    /**
     * @param $id
     * @return View
     */
    public function ticketDetails($id): View
    {
        $title = "Support ticket reply";
        $supportTicket = SupportTicket::with('messages')->findOrFail($id);
        return view('admin.support_ticket.details', compact('title', 'supportTicket'));
    }

    public function ticketReply(Request $request, $id)
    {
        $supportTicket = SupportTicket::findOrFail($id);
        $supportTicket->status = 2;
        $supportTicket->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $supportTicket->id;
        $message->admin_id = Auth::guard('admin')->id();
        $message->message = $request->input('message');
        $message->save();


        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                try {
                    $supportFile = new SupportFile();
                    $supportFile->support_message_id = $message->id;
                    $supportFile->file = uploadNewFile($file, filePath()['ticket']['path']);
                    $supportFile->save();
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Could not upload your ' . $file];
                    return back()->withNotify($notify);
                }
            }
        }

        $mailCode = [
            'ticket_number' => $supportTicket->ticket_number,
            'link' => route('user.ticket.detail', $supportTicket->id),
        ];

        SendMail::MailNotification($supportTicket->user,'SUPPORT_TICKET_REPLY',$mailCode);

        $notify[] = ['success', "Support ticket replied successfully"];
        return back()->withNotify($notify);
    }


    public function closedTicket($id)
    {
        $supportTicket = SupportTicket::findOrFail($id);
        $supportTicket->status = 4;
        $supportTicket->save();

        $notify[] = ['success', "Support ticket has been closed"];
        return back()->withNotify($notify);
    }

    public function supportTicketDownload($id)
    {
        $supportFile = SupportFile::findOrFail(decrypt($id));
        $file = $supportFile->file;
        $path = filePath()['ticket']['path'].'/'.$file;
        $title = slug('file').'-'.$file;
        $mimetype = mime_content_type($path);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($path);
    }


    public function search(Request $request, $scope)
    {
        
        $title = "Support ticket Search";

        $search = $request->input('search');
        $priority = $request->input('priority');
        $status = $request->input('status');
        $searchDate = $request->input('date');
       
        if ($search!="") {
            $supportTickets = SupportTicket::where('subject','like',"%$search%");
        }

        if ($priority!="") {
            $supportTickets = SupportTicket::where('priority','=',"$priority");
        }

        if ($status!="") {
            $supportTickets = SupportTicket::where('status','=',"$status");
        }

        if ($searchDate!="") {
            $searchDate_array = explode('-',$request->date);
            $firstDate = $searchDate_array[0];
            $lastDate = null;
            if (count($searchDate_array)>1) {
                $lastDate = $searchDate_array[1];
            }
            $matchDate = "/\d{2}\/\d{2}\/\d{4}/";
            if ($firstDate && !preg_match($matchDate,$firstDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($lastDate && !preg_match($matchDate,$lastDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($firstDate) {
                $supportTickets = SupportTicket::whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $supportTickets = SupportTicket::whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }

        if ($search=="" && $searchDate=="" && $priority=="" && $status=="") {
            $notify[] = ['error','Search data field empty'];
            return back()->withNotify($notify);
        }

        
        if ($scope == 'running') {
            $supportTickets = $supportTickets->where('status',1);
        }elseif($scope == 'answered'){
            $supportTickets = $supportTickets->where('status',2);
        }elseif($scope == 'replied'){
            $supportTickets = $supportTickets->where('status',3);
        }elseif($scope == 'closed'){
            $supportTickets = $supportTickets->where('status',4);
        }

        $supportTickets = $supportTickets->orderBy('id','desc')->paginate(paginateNumber());


      
        return view('admin.support_ticket.index', compact('title', 'supportTickets', 'status', 'priority', 'search', 'searchDate'));
    }

}
