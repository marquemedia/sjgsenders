<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontendSection extends Model
{
    use HasFactory;

    const PRICING_PLAN_CONTENT = 'pricing_plan.fixed_content';
    const FAQ_CONTENT = 'faq.fixed_content';
    const FAQ_ELEMENT = 'faq.element_content';
    const ABOUT_CONTENT = 'about.fixed_content';
    const ABOUT_ELEMENT = 'about.element_content';

    const BANNER_CONTENT  = 'banner.fixed_content';
    const BANNER_ELEMENT = 'banner.element_content';

    const PROCESS_CONTENT  = 'process.fixed_content';
    const PROCESS_ELEMENT = 'process.element_content';

    const CLIENT_CONTENT  = 'client.fixed_content';
    const CLIENT_ELEMENT = 'client.element_content';


    const FEATURE_CONTENT  = 'feature.fixed_content';
    const FEATURE_ELEMENT = 'feature.element_content';


    const CTA_CONTENT  = 'cta.fixed_content';
    const CTA_ELEMENT = 'cta.element_content';


    const OVERVIEW_CONTENT  = 'over_view.fixed_content';
    const OVERVIEW_ELEMENT = 'over_view.element_content';
    
    const FOOTER_CONTENT  = 'footer.fixed_content';
    const FOOTER_ELEMENT = 'footer.element_content';
    const SOCIAL_ICON = 'social_icon.element_content';
    const PAGES = 'policy_pages.element_content';


    protected $fillable = [
        'uid',
        'section_key',
        'section_value',
        'status',
    ];

    protected $casts = [
        'section_value' => 'json'
    ];
}
