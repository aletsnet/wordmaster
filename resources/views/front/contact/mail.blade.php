<x-mail::message>
{{ __('front.contact_mail_title') }}

<x-mail::table>
| {{ __('front.contact_name') }} | {{ $data['name'] }} |
|:---|:---|
| {{ __('front.contact_email_label') }} | {{ $data['email'] }} |
@if($data['phone'] ?? null)
| {{ __('front.contact_phone') }} | {{ $data['phone'] }} |
@endif
| {{ __('front.contact_message_label') }} | {{ $data['message'] }} |
</x-mail::table>
</x-mail::message>
