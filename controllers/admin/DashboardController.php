<?php
class DashboardController
{
    public function indexAction(): void
    {
        Auth::require();

        $messages     = new ContactMessageModel();
        $testimonials = new TestimonialModel();
        $services     = new ServiceModel();
        $benefits     = new BenefitModel();
        $values       = new OrgValueModel();

        $data = [
            'user'                 => Auth::user(),
            'unread_messages'      => $messages->unreadCount(),
            'total_messages'       => $messages->count(),
            'pending_testimonials' => $testimonials->count('pending'),
            'total_services'       => $services->count(),
            'total_benefits'       => $benefits->count(),
            'total_values'         => $values->count(),
            'recent_messages'      => $messages->latest(5),
        ];

        View::render('admin/dashboard', $data);
    }
}
