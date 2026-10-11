<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\ResumeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;
use Tests\TestCase;

class ResumeDownloadTest extends TestCase
{
    use RefreshDatabase;

    private const string OWNER_EMAIL = 'test@admin.com';

    protected function setUp(): void
    {
        parent::setUp();

        Pdf::fake();

        config(['admin.email' => self::OWNER_EMAIL]);
    }

    public function test_it_downloads_the_owners_published_resume_as_a_pdf(): void
    {
        $owner = User::factory()->create([
            'email' => self::OWNER_EMAIL,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        $resume = Resume::factory()->published()->for($owner)->create();

        $this->get(route('resume.download'))->assertOk();

        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf): bool => $pdf->viewName === 'home'
            && $pdf->viewData['resume']->is($resume)
            && $pdf->format === 'a4'
            && $pdf->isDownload()
            && $pdf->downloadName === 'jane_doe.pdf');
    }

    public function test_it_tells_search_engines_not_to_index_the_pdf(): void
    {
        $owner = User::factory()->create(['email' => self::OWNER_EMAIL]);
        Resume::factory()->published()->for($owner)->create();

        $this->get(route('resume.download'))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_it_embeds_the_roboto_fonts_in_the_pdf(): void
    {
        $owner = User::factory()->create(['email' => self::OWNER_EMAIL]);
        Resume::factory()->published()->for($owner)->create();

        $this->get(route('resume.download'))->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf): bool {
            $html = $pdf->getHtml();

            foreach ([400, 600, 700] as $weight) {
                $this->assertMatchesRegularExpression(
                    "/@font-face\{font-family:\"?Roboto\"?;[^}]*font-weight:$weight;[^}]*src:url\(\"?data:font\/woff2;base64,/",
                    $html,
                );
            }

            $this->assertStringNotContainsString('.woff2', $html);

            return true;
        });
    }

    public function test_the_pdf_includes_work_experience_and_education(): void
    {
        $owner = User::factory()->create(['email' => self::OWNER_EMAIL]);
        $resume = Resume::factory()->published()->for($owner)->create();
        ResumeEntry::factory()->recycle($resume)->create(['organization' => 'Acme']);
        ResumeEntry::factory()->recycle($resume)->education()->create(['organization' => 'University of Ljubljana']);

        $this->get(route('resume.download'))->assertOk();

        Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf): bool {
            $html = $pdf->getHtml();

            $this->assertStringContainsString('Acme', $html);
            $this->assertStringContainsString('>Education</h2>', $html);
            $this->assertStringContainsString('University of Ljubljana', $html);

            return true;
        });
    }

    public function test_it_names_the_file_after_the_users_name_when_first_and_last_name_are_missing(): void
    {
        $owner = User::factory()->create([
            'email' => self::OWNER_EMAIL,
            'name' => 'Jane Doe',
            'first_name' => null,
            'last_name' => null,
        ]);
        Resume::factory()->published()->for($owner)->create();

        $this->get(route('resume.download'))->assertOk();

        Pdf::assertRespondedWithPdf(fn (PdfBuilder $pdf): bool => $pdf->downloadName === 'jane_doe.pdf');
    }

    public function test_it_does_not_download_an_unpublished_resume(): void
    {
        $owner = User::factory()->create(['email' => self::OWNER_EMAIL]);
        Resume::factory()->for($owner)->create();

        $this->get(route('resume.download'))->assertNotFound();
    }

    public function test_it_does_not_download_another_users_resume(): void
    {
        User::factory()->create(['email' => self::OWNER_EMAIL]);
        Resume::factory()->published()->create();

        $this->get(route('resume.download'))->assertNotFound();
    }

    public function test_it_returns_not_found_when_there_is_no_resume(): void
    {
        $this->get(route('resume.download'))->assertNotFound();
    }

    public function test_it_rate_limits_downloads(): void
    {
        $owner = User::factory()->create(['email' => self::OWNER_EMAIL]);
        Resume::factory()->published()->for($owner)->create();

        for ($i = 0; $i < 20; $i++) {
            $this->get(route('resume.download'))->assertOk();
        }

        $this->get(route('resume.download'))->assertTooManyRequests();
    }
}
