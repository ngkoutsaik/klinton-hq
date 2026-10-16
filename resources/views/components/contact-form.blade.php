@use('Illuminate\Support\Facades\Request')
@use('Illuminate\Support\Facades\Session')
@use('Illuminate\Support\Facades\URL')
<section class="resume-section" id="contact">
    <h2 class="section-title">Contact</h2>

    @if(Session::has('contact_status'))
        <p class="contact-status" role="status">{{ Session::get('contact_status') }}</p>
    @endif

    <form method="post" action="{{ URL::route('contact.store') }}" class="contact-form">
        @csrf
        <x-honeypot />

        <div class="contact-field">
            <label for="contact-email">Email</label>
            <input type="email" id="contact-email" name="email" value="{{ Request::old('email') }}" required maxlength="255"
                   autocomplete="email" @error('email') aria-invalid="true" aria-describedby="contact-email-error" @enderror>
            @error('email')
                <p class="contact-error" id="contact-email-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="contact-field">
            <label for="contact-subject">Subject</label>
            <input type="text" id="contact-subject" name="subject" value="{{ Request::old('subject') }}" required maxlength="255"
                   @error('subject') aria-invalid="true" aria-describedby="contact-subject-error" @enderror>
            @error('subject')
                <p class="contact-error" id="contact-subject-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="contact-field">
            <label for="contact-message">Message</label>
            <textarea id="contact-message" name="message" rows="6" required maxlength="5000"
                      @error('message') aria-invalid="true" aria-describedby="contact-message-error" @enderror>{{ Request::old('message') }}</textarea>
            @error('message')
                <p class="contact-error" id="contact-message-error">{{ $message }}</p>
            @enderror
        </div>

        <label class="contact-checkbox">
            <input type="checkbox" name="send_copy" value="1" @checked(Request::old('send_copy'))>
            Send a copy to me
        </label>

        <div>
            <label class="contact-checkbox">
                <input type="checkbox" name="privacy_consent" value="1" required @checked(Request::old('privacy_consent'))
                       @error('privacy_consent') aria-invalid="true" aria-describedby="contact-privacy-error" @enderror>
                <span>
                    I agree to the
                    <button type="button" class="contact-link" popovertarget="privacy-notice">privacy notice</button>
                </span>
            </label>
            @error('privacy_consent')
                <p class="contact-error" id="contact-privacy-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="download-button contact-submit">Send message</button>
    </form>

    <div id="privacy-notice" class="privacy-notice" popover role="dialog" aria-labelledby="privacy-notice-title">
        <h3 id="privacy-notice-title">Privacy notice</h3>
        <p><strong>What this form collects.</strong> When you send a message, the email address, subject and
            message you type are stored, along with whether you asked for a copy. There are no tracking cookies
            or analytics on this form.</p>
        <p><strong>Why.</strong> This is used only to read and reply to your message. The legal basis is the
            legitimate interest in responding to enquiries you choose to send.</p>
        <p><strong>How long it is kept.</strong> The copy stored on this website is deleted automatically within
            seven days after your message is emailed on. Copies may linger a little longer in routine backups
            until those are rotated. The message itself stays in the recipient's inbox, like any email you
            send.</p>
        <p><strong>Who sees it.</strong> Your message is emailed to the site owner and delivered through an
            email provider acting on their behalf. It is never sold or shared with anyone else. If you tick
            &ldquo;send a copy to me&rdquo;, a copy is also emailed to the address you entered.</p>
        <p><strong>Spam checks.</strong> To block automated spam the form briefly inspects your submission,
            including your IP address, but does not store it.</p>
        <p><strong>Your rights.</strong> You can ask to see, correct or delete what you submitted at any time
            by emailing <a href="mailto:hello@klinton.dev">hello@klinton.dev</a>. You may also complain to your
            local data protection authority.</p>
        <button type="button" class="download-button" popovertarget="privacy-notice" popovertargetaction="hide">Close</button>
    </div>
</section>
