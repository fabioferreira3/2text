<?php

return [
    'blog_first_pass' => "以:tone的语气写一篇博客文章，使用<p>标签来包围段落，使用<h2>标签来包围主要话题和<h3>标签来包围内部话题，基于以下大纲： \n\n:outline\n\n\n进一步的指示：不要用p标签包围h2和h3标签，例如： \n\n错误的输出：\n<p><h2>主题</h2></p>\n\n错误的输出：\n<p><h2>主题</h2><h3>内部主题</h3></p>\n\n\n大纲结构应该被解析到html标签如下：\n\n输入：\nA. 话题1\n\n输出：<h3>A. 话题1</h3>\n\n输入：\nB. 话题2\n\n输出：<h3>B. 话题2</h3>",
    'expand_text' => "使用:tone的语气，使用<h3>标签来表示子话题和<p>标签表示段落，扩展： \n\n:context\n\n\n进一步的指示：\n-在扩展文本时，不要创建新的<h3>内部主题。相反，增加段落的数量。",
    'given_following_text' => "给出以下文本：\n\n:text",
    'given_following_context' => "并给出以下背景：\n\n:context\n\n\n",
    'keyword_instructions' => "- 使用关键字\":keyword\"。\n",
    'max_words' => "- 文字必须最多为 :max 个词\n",
    'more_instructions' => "- 遵循以下其他的发布创建指示：\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- 元描述必须基于以下背景：\n\n\n :context\n\n\n",
    'post_context_instructions' => "- 发布必须基于以下背景：\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- 发布必须有:tone的语气。\n",
    'simplify_text' => "简化以下文本：\n\n:text",
    'style_instructions' => "- 使用 :style 的写作风格\n",
    'summarize_text' => "总结以下文本：\n\n :text",
    'tone_instructions' => "- 使用 :tone 的语气",
    'write_meta_description' => "写一个最多20个词的元描述。使用以下的写作指示：\n\n",
    'write_outline' => "创建一个深入全面的博客文章大纲，使用罗马数字表示主要话题，使用字母表示子话题。大纲只应包含:maxsubtopics主题。大纲应具有:tone的语气。大纲的最后应该包含一个结论主题。不要嵌套第三级主题。不要在用字母标记的子主题内添加内部主题，例如： \n\n好的输出：\nI. 主要主题 \n A. 子主题1 \n B. 子主题2 \n C. 子主题3 \n\n错误的输出：\nI. 主要主题 \nA. 子主题1 \nB. 子主题2\n   1. 内部主题1\n   2. 内部主题2\nC. 子主题3\n\n\n 大纲应基于以下文本： \n\n:context",
    'write_social_media_post' => "为:platform写一篇社交媒体的文章。使用以下的写作指示：\n\n",
    'write_title' => "为以下文本写一个最多7个词的标题： \n\n:context\n\n\n好的和错误的输出的示例：\n\n错误的输出：\n标题：这是标题\n\n好的输出：\n这是标题\n\n进一步的指示：\n\n- 标题必须有:tone的语气。\n",
];
