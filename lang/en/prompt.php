<?php

return [
    'blog_first_pass' => "Write a blog article, using a :tone tone, using <p> tags to surround paragraphs, <h2> tags to surround main topics and <h3> tags to surround inner topics, based on the following outline: \n\n:outline\n\n\nFurther instructions: Do not surround h2 and h3 tags with p tags, for example: \n\n Bad output:\n<p><h2>Topic</h2></p>\n\nBad output:\n<p><h2>Topic</h2><h3>Inner topic</h3></p>\n\n\nThe outline structure should be parsed to html tags like this:\n\nInput:\nA. Topic 1\n\nOutput:<h3>A. Topic 1</h3>\n\nInput:\nB. Topic 2\n\nOutput:<h3>B. Topic 2</h3>\n\n",
    'expand_text' => "Using a :tone tone,, and using <h3> tags for subtopics and <p> tags for paragraphs, expand on: \n\n:context\n\n\nFurther instructions:\n-When expanding the text, do not create new <h3> inner topics. Instead, increase the number of paragraphs.\n\n",
    'given_following_text' => "Given the following text:\n\n:text\n\n",
    'given_following_context' => "And given the following context:\n\n:context\n\n\n",
    'keyword_instructions' => "- Use the keyword \":keyword\".\n",
    'max_words' => "- The text must have a maximum of :max words\n",
    'more_instructions' => "- Follow these others instructions on the post creation:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- The meta description must be based on the following context:\n\n\n :context\n\n\n",
    'paraphrase_text' => 'Paraphrase the following text using a :tone tone:\n\n\n:text',
    'post_context_instructions' => "- The post must be based on the following context:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- The post must have a :tone tone.\n",
    'simplify_text' => "Simplify the following text:\n\n:text",
    'summarize_text' => "Summarize the following text:\n\n :text",
    'tone_instructions' => "- Use a :tone tone.\n",
    'translate_text' => "Translate the following text to :target_language :\n\n:text",
    'write_meta_description' => "Write a meta description using a maximum of 20 words.\n Follow these instructions to guide your writing:\n\n",
    'write_outline' => "Create a comprehensive :style outline for a blog post.\n\n
        - It must have a maximum of two levels.\n
        - Use roman numerals to indicate main topics and alphabet letters to indicate subtopics.\n
        - It must have only :maxsubtopics main topic(s), using the keyword \":keyword\".\n
        - Each main topic must contain a maximum of 2 subtopics.\n
        - The outline must have a \":tone\" tone.\n
        - The outline must not have more than :maxsubtopics main topics, which are represented by the roman numerals.\n
        - Do not nest a third level of topics.\n
        - Do not add inner topics inside the subtopics indicated by alphabet letters, for example: \n\nGood output:\nI. Main Topic \n A. Subtopic 1 \n B. Subtopic 2 \n C. Subtopic 3 \n\nBad output:\nI. Main Topic \nA. Subtopic 1 \nB. Subtopic 2\n   1. Inner topic 1\n   2. Inner topic 2\nC. Subtopic 3\n\n\n
        - The outline should be based on the following text: \n
        --- START OF TEXT ---
        \n\n:context\n\n
        --- END OF TEXT ---",
    'write_social_media_post' => "Write a social media post for :platform. Use the following instructions to guide your writing:\n\n",
    'write_title' => "Write a title, with a maximum of 7 words, for the following text: \n\n:context\n\n\nExamples of good and bad outputs:\n\nBad output:\nTitle: This is the title\n\nGood output:\nThis is the title\n\nFurther instructions:\n\n- The title must have a :tone tone.\n",
];
