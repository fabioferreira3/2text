<?php

return [
    'academic_tone' => 'The complexity of the text must be similar to a scientific article',
    'adventurous_tone' => 'Use a writing tone that makes the reader feel excited, similar to the tone used in travel blogs',
    'append_inst' => "\n\n - Additional instructions: Provide a response with increased burstiness and perplexity and at a slightly higher temperature than your default writing style.",
    'blog_first_pass' => ":first_pass",
    'blog_embedded_first_pass' => "Based on the provided context, y:first_pass",
    'casual_tone' => 'Use a writing tone that makes the reader feel like they are talking to a friend',
    'dramatic_tone' => 'Use a writing tone that makes the reader feel like they are watching a dramatic movie',
    'default_tone' => 'Use a tone that suits the context',
    'expand_text' => "Expand the text following these instructions:\n\n
         - Use a :tone tone.\n
         - Use a : style writing style.\n
         - The keyword \":keyword\" (ignoring the quotes) must be present throughout the text.\n
         - Write three new paragraphs.\n
         - Do not create new <h2> topics.\n
         - This is the text that must be expanded:\n\n
            :context",
    'expand_embedded_text' => "Based on the provided context, :expand_text",
    'expand' => "Rewrite the following text, keeping the same tone, and using twice as many words:\n\n :text",
    'expand_title' => "Rewrite the following title, keeping the same tone, and making it slightly longer:\n\n :text",
    'first_pass' => "Write a simple blog article, following these instructions:\n\n
        - :tone_instructions
        - Use <p> tags to surround paragraphs\n
        - Use <h2> tags to surround main topics\n
        - Do not use <h3> tags\n
        - Write only one paragraph <p> per topic <h2>\n
        - Do not surround h2 tags with p tags, for example: \n\n
            Bad output:\n
                <p><h2>Topic</h2></p>\n\n
        - Main topics in the outline are referenced in the outline as \"Topic\", and the context of each topic are reference under \"Context\". All topics must be converted to a <h2> tag. For example:\n\n
           - Outline structure input:\n
           Topic: Introduction\n
           Context: The nature of apples. Why apples are red.\n\n
           Topic: Nutrition of Apples\n
           Context: Vitamins and minerals. Why apples are good for you.\n\n
           Topic: Production of Apples\n
           Context: How apples are grown. Where apples are grown.\n\n\n

           - Blog post output:\n
           <h2>Introduction</h2><p>Content about nature of apples.</p><p>Content about why apples are red</p>\n
           <h2>Nutrition of Apples</h2><p>Content about vitamins of apples</p><p>Content about benefits of apples</p>\n
           <h2>Production of Apples</h2><p>Content about how apples are grown</p><p>Content about where apples are grown</p>\n\n
        - This is the outline that the blog post must be based: \n\n
            :outline",
    'formal_tone' => 'Use a writing tone that makes the reader feel like they are reading from a serious source like a newspaper',
    'funny_tone' => 'Use a writing tone that makes the reader laught sometimes but not always. A slightly funny tone, while not joking all the time',
    'generic_prompt' => ":prompt\n\n",
    'given_following_text' => "Given the following text:\n\n:text\n\n",
    'given_following_context' => "And given the following context:\n\n:context\n\n\n",
    'increase_complexity' => "Rewrite the following text increasing its reading complexity so a college professor would understand:\n\n :text",
    'keyword_instructions' => "- The following keyword should have focus and be present throughout the post: \":keyword\".\n",
    'max_words' => "- The text must have a maximum of :max words\n",
    'more_instructions' => "- Follow these others instructions for the creation of the post:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- The meta description must be based on the following outline:\n\n\n :\n\n\n",
    'mysterious_tone' => 'Use a writing tone that makes the reader feel like they are reading a mystery novel or watching a mystery movie. A tone that makes the reader feel intrigued',
    'optimistic_tone' => 'Use a writing tone that makes the reader feel like they are reading a motivational book, highlighting the positive aspects of the topic',
    'outline_base' => "Create a comprehensive :style outline for a blog post.\n\n
        - It must have a maximum of two levels.\n
        - Use numbers to indicate main topics and alphabet letters to indicate subtopics.\n
        - It must have only :maxsubtopics main topic(s), using the keyword \":keyword\".\n
        - Each main topic must contain a maximum of 2 subtopics.\n
        - The outline must have a \":tone\" tone.\n
        - The outline must not have more than :maxsubtopics main topics, which are represented by numbers.\n
        - Do not nest a third level of topics.\n
        - Do not add inner topics inside the subtopics indicated by alphabet letters, for example: \n\nGood output:\n1. Main Topic \n A. Subtopic 1 \n B. Subtopic 2 \n C. Subtopic 3 \n\nBad output:\n1. Main Topic \nA. Subtopic 1 \nB. Subtopic 2\n   B1. Inner topic 1\n   B2. Inner topic 2\nC. Subtopic 3\n\n\n",
    'paraphrase_text' => "Paraphrase the following text, while using a :tone tone, keeping the same number of words and keeping its original language. Just provide the result, without making any additional comments. This is the text:\n\n\n:text",
    'pessimistic_tone' => 'Use a writing tone that makes the reader feel like they are reading a book that highlights the negative aspects of the topic',
    'reduce_complexity' => "Rewrite the following text reducing its reading complexity so a 13 year old kid would understand:\n\n :text",
    'sarcastic_tone' => 'Use a writing tone that makes the reader feel like they are reading a sarcastic book or watching a sarcastic movie. A tone that makes the reader feel like they are being mocked',
    'shorten' => "Rewrite the following text, keeping the same tone and making it shorter:\n\n :text",
    'shorten_title' => "Rewrite the following title, keeping the same tone and making it shorter:\n\n :text",
    'simplify_text' => "Simplify the following text while keeping the same tone:\n\n:text",
    'simplistic_tone' => 'The reading complexity of the text must be low, as if a teenager would be able to read and understand it',
    'summarize_text' => "Summarize the following text:\n\n :text",
    'tone_instructions' => "- Use a :tone tone.\n",
    'translate_text' => "Translate the following text to :target_language :\n\n:text",
    'write_meta_description' => "Write a meta description using a maximum of 20 words.\n Follow these instructions to guide your writing:\n\n",
    'write_embbeded_outline' => "Based on the context provided, :outline_base",
    'write_outline' => ":outline_base\n - The outline should be based on the following text: \n
        --- START OF TEXT ---
        \n\n:context\n\n
        --- END OF TEXT ---",
    'write_title' => "Write a title, with a maximum of 7 words, for the following text: \n\n:context\n\n\nExamples of good and bad outputs:\n\nBad output:\nTitle: This is the title\n\nGood output:\nThis is the title\n\nFurther instructions:\n\n- The title must have a :tone tone.\n",
    'write_embedded_title' => "Based on the provided context, write a title, with a maximum of 7 words.\nExamples of good and bad outputs:\n\nBad output:\nTitle: This is the title\n\nGood output:\nThis is the title\n\nFurther instructions:\n\n- The title must have a :tone tone.\n"
];
