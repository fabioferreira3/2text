<?php

return [
    'blog_first_pass' => ":tone 톤으로 블로그 게시글을 작성하십시오. <p> 태그로 단락을 나타내고, <h2> 태그로 주요 주제를 나타내며, <h3> 태그로 내부 주제를 나타내는 다음 개요에 따라 작성하십시오: \n\n:outline\n\n\n추가 지침: h2 및 h3 태그를 p 태그로 둘러싸지 마십시오. 예를 들어: \n\n잘못된 출력:\n<p><h2>주제</h2></p>\n\n잘못된 출력:\n<p><h2>주제</h2><h3>내부 주제</h3></p>\n\n\n개요 구조는 다음과 같이 HTML 태그로 변환해야 합니다:\n\n입력:\nA. 주제 1\n\n출력:<h3>A. 주제 1</h3>\n\n입력:\nB. 주제 2\n\n출력:<h3>B. 주제 2</h3>",
    'expand_text' => ":tone의 톤을 사용하여, <h3> 태그로 하위 주제를, <p> 태그로 문단을 나타내어 다음을 확장하십시오: \n\n:context\n\n\n추가 지침:\n- 텍스트를 확장할 때 새로운 <h3> 내부 주제를 만들지 마십시오. 대신 문단의 수를 늘리십시오.",
    'given_following_text' => "다음 텍스트가 제공되었습니다:\n\n:text",
    'given_following_context' => "그리고 다음과 같은 맥락이 제공되었습니다:\n\n:context\n\n\n",
    'keyword_instructions' => "- 키워드 \":keyword\"를 사용하십시오.\n",
    'more_instructions' => "- 다음과 같은 추가 게시물 생성 지침을 따르십시오:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- 메타 설명은 다음 배경에 기반해야 합니다:\n\n\n :context\n\n\n",
    'post_context_instructions' => "- 게시물은 다음 배경에 기반해야 합니다:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- 게시물은 :tone 톤을 가져야 합니다.\n",
    'simplify_text' => "다음 텍스트를 단순화하십시오:\n\n:text",
    'summarize_text' => "다음 텍스트를 요약하십시오:\n\n :text",
    'write_meta_description' => "최대 20단어의 메타 설명을 작성하십시오. 다음과 같은 작성 지침을 사용하십시오:\n\n",
    'write_outline' => "로마 숫자를 사용하여 주요 주제를, 알파벳을 사용하여 하위 주제를 표시하는 깊고 포괄적인 블로그 게시물 개요를 작성하십시오. 개요에는 :maxsubtopics 주제만 포함해야 합니다. 개요는 :tone 톤을 가져야 합니다. 개요의 마지막에는 결론 주제가 포함되어야 합니다. 세 번째 수준 주제를 중첩하지 마십시오. 예를 들어, 알파벳으로 표시된 하위 주제 내부에 내부 주제를 추가하지 마십시오. 예:\n\n좋은 출력:\nI. 주요 주제 \n A. 하위 주제 1 \n B. 하위 주제 2 \n C. 하위 주제 3 \n\n잘못된 출력:\nI. 주요 주제 \nA. 하위 주제 1 \nB. 하위 주제 2\n   1. 내부 주제 1\n   2. 내부 주제 2\nC. 하위 주제 3\n\n\n 개요는 다음 텍스트를 기반으로 해야 합니다: \n\n:context",
    'write_social_media_post' => ":platform에 대한 소셜 미디어 게시물을 작성하십시오. 다음과 같은 작성 지침을 사용하십시오:\n\n",
    'write_title' => "다음에 대해 7단어 이내의 제목을 작성하십시오: \n\n:context\n\n\n잘못된 출력과 올바른 출력의 예는 다음과 같습니다:\n\n잘못된 출력:\n제목: 이것은 제목입니다\n\n올바른 출력:\n이것은 제목입니다\n\n추가 지침:\n\n- 제목은 :tone 톤을 가져야 합니다.\n",
];
