<?php

use App\Enums\DocumentType;
use App\Helpers\MediaHelper;
use App\Models\Document;

beforeEach(function () {
    $this->mediaHelper = new MediaHelper();
});

describe('MediaHelper helper', function () {
    it('gets social media image size per platform', function () {
        $instagramSizes = $this->mediaHelper->getSocialMediaImageSize('instagram');
        expect($instagramSizes)->toBe(['height' => 1024, 'width' => 1024]);

        $linkedinSizes = $this->mediaHelper->getSocialMediaImageSize('linkedin');
        expect($linkedinSizes)->toBe(['height' => 1024, 'width' => 1792]);

        $facebookSizes = $this->mediaHelper->getSocialMediaImageSize('facebook');
        expect($facebookSizes)->toBe(['height' => 1024, 'width' => 1792]);

        $twitterSizes = $this->mediaHelper->getSocialMediaImageSize('twitter');
        expect($twitterSizes)->toBe(['height' => 1024, 'width' => 1792]);

        $defaultSizes = $this->mediaHelper->getSocialMediaImageSize('default');
        expect($defaultSizes)->toBe(['height' => 1024, 'width' => 1024]);
    });

    it('gets image sizes by document type', function () {
        $blogPost = Document::factory()->create(['type' => DocumentType::BLOG_POST->value]);
        $blogPostSizes = $this->mediaHelper->getImageSizeByDocumentType($blogPost);
        expect($blogPostSizes)->toBe([
            'height' => 1024,
            'width' => 1024
        ]);

        $socialMediaPost = Document::factory()->create([
            'type' => DocumentType::SOCIAL_MEDIA_POST->value,
            'meta' => ['platform' => 'linkedin']
        ]);
        $socialMediaPostSizes = $this->mediaHelper->getImageSizeByDocumentType($socialMediaPost);
        expect($socialMediaPostSizes)->toBe([
            'height' => 1024,
            'width' => 1024
        ]);

        $defaultDoc = Document::factory()->create(['type' => DocumentType::AUDIO_TRANSCRIPTION->value]);
        $defaultDocSizes = $this->mediaHelper->getImageSizeByDocumentType($defaultDoc);
        expect($defaultDocSizes)->toBe([
            'height' => 1024,
            'width' => 1024
        ]);
    });
})->group('helpers');
