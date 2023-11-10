<?php

namespace App\Helpers;

class InstructionsHelper
{
    public static function heroImages()
    {
        return "<div class='flex flex-col gap-4'>
            <div>
                <h3 class='font-bold'>" . __('instructions.hero_images_1_header') . "</h3>
                <p>" . __('instructions.hero_images_1_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.hero_images_2_header') . "</h3>
                <p>" . __('instructions.hero_images_2_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.hero_images_3_header') . "</h3>
                <p>" . __('instructions.hero_images_3_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.hero_images_4_header') . "</h3>
                <p>" . __('instructions.hero_images_4_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.hero_images_5_header') . "</h3>
                <p>" . __('instructions.hero_images_5_content') . "</p>
            </div>
        </div>";
    }

    public static function imageGuidelines()
    {
        return "<div class='flex flex-col gap-4'>
            <div>
                <h3 class='font-bold'>" . __('instructions.image_guideline_1_header') . "</h3>
                <p>" . __('instructions.image_guideline_1_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.image_guideline_2_header') . "</h3>
                <p>" . __('instructions.image_guideline_2_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.image_guideline_3_header') . "</h3>
                <p>" . __('instructions.image_guideline_3_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.image_guideline_4_header') . "</h3>
                <p>" . __('instructions.image_guideline_4_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.image_guideline_5_header') . "</h3>
                <p>" . __('instructions.image_guideline_5_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.image_guideline_6_header') . "</h3>
                <p>" . __('instructions.image_guideline_6_content') . "</p>
            </div>
            <div>
                <h3 class='font-bold'>" . __('instructions.image_guideline_7_header') . "</h3>
                <p>" . __('instructions.image_guideline_7_content') . "</p>
            </div>
        </div>";
    }

    public static function transcriptionSource()
    {
        return "Define the source of the transcription. Currently, only Youtube videos are supported.";
    }

    public static function transcriptionLanguage()
    {
        return "<p>You need to inform the language of the video since I'm currently not able to auto-discover its main language.</p>";
    }

    public static function socialMediaPlatforms()
    {
        return "<p>" . __('instructions.choose_platforms') . "</p>";
    }

    public static function socialMediaLanguages()
    {
        return "<p>" . __('social_media.define_language') . "</p><p>" . __('social_media.selected_language_info') . "</p>";
    }

    public static function blogLanguages()
    {
        return "<p>" . __('blog.define_language') . "</p><p class='mt-2 text-sm'>" . __('blog.selected_language_info') . "</p>";
    }

    public static function socialMediaKeyword()
    {
        return __('social_media.define_keyword');
    }

    public static function blogKeyword()
    {
        return __('instructions.provide_keyword');
    }

    public static function maxSubtopics()
    {
        return '<p>' . __('instructions.define_subtopics') . '<p><h3 class="font-bold text-sm mt-3">
        ' . __('instructions.note') . '</h3>
        <p class="text-sm">' . __('instructions.subtopics_estimate') . '</p>';
    }

    public static function socialMediaGeneral()
    {
        return '<p>' . __('instructions.fill_information') . '</p>

        <h3 class="font-bold">' . __('instructions.target_platforms') . '</h3><p class="text-sm">' . __('instructions.choose_platforms') . '</p>

        <h3 class="font-bold">' . __('instructions.source') . '</h3><p class="text-sm">' . __('instructions.provide_source') . '</p>

        <h3 class="font-bold">' . __('instructions.keyword') . '</h3><p class="text-sm">' . __('instructions.provide_keyword') . '</p>

        <h3 class="font-bold">' . __('instructions.language') . '</h3><p class="text-sm">' . __('instructions.select_language') . '</p>

        <h3 class="font-bold">' . __('instructions.tone') . '</h3><p class="text-sm">' . __('instructions.define_tone_info') . '</p>';
    }

    public static function blogGeneral()
    {
        return '<p>' . __('instructions.fill_information') . '</p>

        <h3 class="font-bold">' . __('instructions.source') . '</h3><p class="text-sm">' . __('instructions.provide_source') . '</p>

        <h3 class="font-bold">' . __('instructions.keyword') . '</h3><p class="text-sm">' . __('instructions.provide_keyword') . '</p>

        <h3 class="font-bold">' . __('instructions.number_topics') . '</h3><p class="text-sm">' . __('instructions.indicate_topics_number') . '</p>

        <h3 class="font-bold">' . __('instructions.language') . '</h3><p class="text-sm">' . __('instructions.select_language') . '</p>

        <h3 class="font-bold">' . __('instructions.tone') . '</h3><p class="text-sm">' . __('instructions.define_tone_info') . '</p>';
    }

    public static function sources()
    {
        return "
        <h3 class='mt-4 font-bold'>Youtube videos</h3>
        <p>" . __('instructions.new_text_enter_youtube_link') . "</p>
        <h3 class='mt-4 font-bold'>" . __('instructions.website_url') . "</h3>
        <p>" . __('instructions.new_text_enter_external_url') . "</p>
        <h3 class='mt-4 font-bold'>" . __('instructions.pdf_files') . "</h3>
        <p>" . __('instructions.new_text_pdf_files') . "</p>
        <h3 class='mt-4 font-bold'>" . __('instructions.docx_files') . "</h3>
        <p>" . __('instructions.new_text_docx_files') . "</p>
        <h3 class='mt-4 font-bold'>" . __('instructions.csv_files') . "</h3>
        <p>" . __('instructions.new_text_csv_files') . "</p>
        <h3 class='mt-4 font-bold'>" . __('instructions.free_text') . "</h3>
        <p>" . __('instructions.new_text_enter_text') . "</p>";
    }

    public static function paraphraserSources()
    {
        return "<p>" . __('instructions.define_base_context_new_paraphraser') . "</p>";
    }

    public static function writingTones()
    {
        return "<p>" . __('instructions.define_tone') . "<p>
        <h3 class='font-bold mt-4'>" . __('instructions.useful_guidelines') . "</h3>
            <ul class='mt-2'>
                <li>" . __('instructions.consider_readers') . "</li>
                <li>" . __('instructions.serious_topic') . "</li>
                <li>" . __('instructions.telling_story') . "</li>
                <li>" . __('instructions.expected_reaction') . "</li>
            </ul>";
    }

    public static function writingStyles()
    {
        return "
        <div class='font-bold pb-2'>" . __('styles.descriptive') . "</div>
            <ul class='list-disc px-4 text-sm'>
                <li>" . __('instructions.depict_imagery') . "</li>
                <li>" . __('instructions.poetry') . "</li>
            </ul>
        <div class='font-bold mt-2 pb-2'>" . __('styles.expository') . "</div>
            <ul class='list-disc px-4 text-sm'>
                <li>" . __('instructions.explain_concept') . "</li>
                <li>" . __('instructions.express_opinions') . "</li>
                <li>" . __('instructions.textbooks') . "</li>
            </ul>
        <div class='font-bold mt-2 pb-2'>" . __('styles.narrative') . "</div>
            <ul class='list-disc px-4 text-sm'>
                <li>" . __('instructions.share_information') . "</li>
                <li>" . __('instructions.short_stories') . "</li>
            </ul>
        <div class='font-bold mt-2 pb-2'>" . __('styles.persuasive') . "</div>
            <ul class='list-disc px-4 text-sm'>
                <li>" . __('instructions.convince_reader') . "</li>
                <li>" . __('instructions.letters_recommendation') . "</li>
            </ul>
        ";
    }
}
