<?php

namespace App\Helpers;

class InstructionsHelper
{
    public static function socialMediaPlatforms()
    {
        return "<h2 class='font-bold text-lg'>Target Platforms</h2><p>Choose for which platforms you would like me to write a post. For each selected platform I will create a different post.</p>";
    }

    public static function socialMediaGeneral()
    {
        return '<p>Please fill out the following information so I can understand your requirements and write you an unique and high-quality post.</p>

        <h3 class="font-bold">Target platforms</h3><p class="text-sm">Choose for which platforms you would like me to write a post. For each selected platform I will create a different post.</p>

        <h3 class="font-bold">Source</h3><p class="text-sm">Provide a source of the context that your post should be based on. It could be a YouTube link, an external web page or just free text.</p>

        <h3 class="font-bold">Keyword</h3><p class="text-sm">Provide a keyword that you would like me to use throughout your post. This keyword will help me generate a relevant and focused article.</p>

        <h3 class="font-bold">Language</h3><p class="text-sm">Select the language you would like the post to be generated in. If you have provided a YouTube link, please ensure that the selected language matches the main language of the video.</p>

        <h3 class="font-bold">Tone</h3><p class="text-sm">Define the tone of your post. You may pick from casual, funny, sarcastic, dramatic, academic, and other tones. This will help me write a post that is in line with your preference and your audience\'s.</p>';
    }

    public static function blogGeneral()
    {
        return '<p>Please fill out the following information so I can understand your requirements and write you an unique and high-quality blog post.</p>

        <h3 class="font-bold">Source</h3><p class="text-sm">Provide a source of the context that your blog post should be based on. It could be a YouTube link, an external web page or just free text.</p>

        <h3 class="font-bold">Keyword</h3><p class="text-sm">Provide a keyword that you would like me to use throughout your blog post. This keyword will help me generate a relevant and focused article.</p>

        <h3 class="font-bold">Number of Topics</h3><p class="text-sm">Indicate the number of topics you would like me to cover in your blog post. You may define a minimum of one and a maximum of fifteen topics.</p>

        <h3 class="font-bold">Language</h3><p class="text-sm">Select the language you would like the blog post to be generated in. If you have provided a YouTube link, please ensure that the selected language matches the main language of the video.</p>

        <h3 class="font-bold">Tone</h3><p class="text-sm">Define the tone of your blog post. You may pick from casual, funny, sarcastic, dramatic, academic, and other tones. This will help me write a blog post that is in line with your preference and your audience\'s.</p>';
    }

    public static function sources()
    {
        return "<h2 class='font-bold text-lg'>Source</h2>
        <p>Define what would be the base context of your text. Choose between a youtube link, a website url, or just from free text.</p>
        <h3 class='mt-4 font-bold'>Youtube</h3>
        <p>Enter a youtube link and I'll write text based on the content of the video.</p>
        <h3 class='mt-4 font-bold'>Website URL</h3>
        <p>Enter an external website url to be used as context, like another blog post or page.
        I'll do my best to extract as much information as possible from that page and use it as context for the creation of your text.</p>
        <h3 class='mt-4 font-bold'>Free text</h3>
        <p>Just enter any text that you want as context and I'll write a text based on the
        information you provide.</p>";
    }

    public static function writingTones()
    {
        return "<h2 class='font-bold'>Tone</h2><p>Define the tone of the writing.<p>
        <h3 class='font-bold text-sm'>Useful guidelines</h3>
            <ul>
                <li>Take into account your readers.</li>
                <li>Is it a serious topic? Or could be a fun one?</li>
                <li>Are you telling a history? Of what genre?</li>
                <li>What is the reaction you expect from your readers?</li>
            </ul>";
    }

    public static function writingStyles()
    {
        return "<h2 class='font-bold'>Style</h2><p>Define the writing style.<p>
        <div class='font-bold'>Descriptive</div>
            <ul class='list-disc px-4 text-sm'>
                <li>Used to depict imagery to create a clear picture in the mind of the reader.</li>
                <li>Employs literary techniques such as similes, metaphors, allegory, etc to engage the audience.</li>
                <li>Poetry; fictional novels or plays; memoirs or first-hand accounts of events</li>
            </ul>
        <div class='font-bold'>Expository</div>
            <ul class='list-disc px-4 text-sm'>
                <li>Used to explain a concept and share information to a broader audience.</li>
                <li>This type is not meant to express opinions.</li>
                <li>How-to articles; textbooks; news stories; business, technical, or scientific writing</li>
            </ul>
        <div class='font-bold'>Narrative</div>
            <ul class='list-disc px-4 text-sm'>
                <li>Share information in the context of a story.</li>
                <li>Usually includes characters, conflicts, and settings.</li>
                <li>Short stories; novels; poetry; historical accounts </li>
            </ul>
        <div class='font-bold'>Persuasive</div>
            <ul class='list-disc px-4 text-sm'>
                <li>Aims to convince the reader of the validity of a certain position or argument.</li>
                <li>Includes the writers’ opinions, and provides justifications and evidence to support their claims.</li>
                <li>Letters of recommendation; cover letters; newspaper articles; argumentative essays for academic papers</li>
            </ul>";
    }
}
