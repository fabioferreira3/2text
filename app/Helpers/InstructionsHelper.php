<?php

namespace App\Helpers;

class InstructionsHelper
{
    public static function socialMediaPlatforms()
    {
        return "<h2 class='font-bold text-lg'>" . __('instructions.target_platforms') . "</h2><p>" . __('instructions.choose_platforms') . "</p>";
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
        return "<h2 class='font-bold text-lg'>" . __('instructions.source') . "</h2>
        <p>" . __('instructions.define_base_context') . "</p>
        <h3 class='mt-4 font-bold'>Youtube</h3>
        <p>" . __('instructions.enter_youtube_link') . "</p>
        <h3 class='mt-4 font-bold'>" . __('instructions.website_url') . "</h3>
        <p>" . __('instructions.enter_external_url') . "</p>
        <h3 class='mt-4 font-bold'>" . __('instructions.free_text') . "</h3>
        <p>" . __('instructions.enter_text') . "</p>";
    }

    public static function writingTones()
    {
        return "<h2 class='font-bold'>" . __('instructions.tone') . "</h2><p>" . __('instructions.define_tone') . "<p>
        <h3 class='font-bold text-sm'>" . __('instructions.useful_guidelines') . "</h3>
            <ul>
                <li>" . __('instructions.consider_readers') . "</li>
                <li>" . __('instructions.serious_topic') . "</li>
                <li>" . __('instructions.telling_story') . "</li>
                <li>" . __('instructions.expected_reaction') . "</li>
            </ul>";
    }

    public static function writingStyles()
    {
        return "<h2 class='font-bold'>Style</h2><p>" . __('instructions.define_style') . "<p>
        <div class='font-bold'>" . __('styles.descriptive') . "</div>
            <ul class='list-disc px-4 text-sm'>
                <li>" . __('instructions.depict_imagery') . "</li>
                <li>" . __('instructions.literary_techniques') . "</li>
                <li>" . __('instructions.poetry') . "</li>
            </ul>
        <div class='font-bold'>" . __('styles.expository') . "</div>
            <ul class='list-disc px-4 text-sm'>
                <li>" . __('instructions.explain_concept') . "</li>
                <li>" . __('instructions.express_opinions') . "</li>
                <li>" . __('instructions.textbooks') . "</li>
            </ul>
        <div class='font-bold'>" . __('styles.narrative') . "</div>
            <ul class='list-disc px-4 text-sm'>
                <li>" . __('instructions.share_information') . "</li>
                <li>" . __('instructions.includes_characters') . "</li>
                <li>" . __('instructions.short_stories') . "</li>
            </ul>
        <div class='font-bold'>" . __('styles.persuasive') . "</div>
            <ul class='list-disc px-4 text-sm'>
                <li>" . __('instructions.convince_reader') . "</li>
                <li>" . __('instructions.includes_opinions') . "</li>
                <li>" . __('instructions.letters_recommendation') . "</li>
            </ul>";
    }
}
