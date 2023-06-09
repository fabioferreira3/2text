<?php

return [
    'blog_first_pass' => ":tone tonlamasını kullanarak, paragrafları çevrelemek için <p> etiketleri, ana konuları çevrelemek için <h2> etiketleri ve iç konuları çevrelemek için <h3> etiketleri kullanarak bir blog yazısı yazın. Şu taslak üzerine dayanmalıdır: \n\n:outline\n\n\nEk talimatlar: h2 ve h3 etiketlerini p etiketleriyle çevrelemeyin. Örneğin: \n\n Yanlış çıktı:\n<p><h2>Konu</h2></p>\n\nYanlış çıktı:\n<p><h2>Konu</h2><h3>İç konu</h3></p>\n\n\nTaslak yapısı, HTML etiketlerine aşağıdaki gibi çevrilmelidir:\n\nGiriş:\nA. Konu 1\n\nÇıktı:<h3>A. Konu 1</h3>\n\nGiriş:\nB. Konu 2\n\nÇıktı:<h3>B. Konu 2</h3>",
    'expand_text' => ":tone tonlamasını kullanın ve <h3> etiketlerini alt konular için, <p> etiketlerini paragraflar için kullanarak şunları genişletin: \n\n:context\n\n\nDaha fazla talimatlar:\n-Yazıyı genişletirken yeni <h3> iç konular oluşturmayın. Bunun yerine paragraf sayısını artırın.",
    'given_following_text' => "Aşağıdaki metne göre:\n\n:text",
    'given_following_context' => "Ve aşağıdaki bağlama göre:\n\n:context\n\n\n",
    'keyword_instructions' => "- \":keyword\" anahtar kelimesini kullanın.\n",
    'max_words' => "- Metin en fazla :max kelime içermelidir\n",
    'more_instructions' => "- Bu diğer talimatları gönderi oluşturmada takip edin:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- Meta açıklaması aşağıdaki bağlama dayanmalıdır:\n\n\n :context\n\n\n",
    'post_context_instructions' => "- Gönderi aşağıdaki bağlama dayanmalıdır:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- Gönderinin bir :tone tonlaması olmalıdır.\n",
    'simplify_text' => "Aşağıdaki metni basitleştirin:\n\n:text",
    'style_instructions' => "- :style yazı stili kullanın\n",
    'summarize_text' => "Aşağıdaki metni özetleyin:\n\n :text",
    'tone_instructions' => "- :tone tonunu kullanın",
    'write_meta_description' => "En fazla 20 kelimeden oluşan bir meta açıklama yazın. Yazınızı yönlendirmek için aşağıdaki talimatları kullanın:\n\n",
    'write_outline' => "En fazla iki seviyeli, ana konuları belirten Roma rakamları ve alt konuları belirten alfabetik harfler kullanarak derinlemesine ve kapsamlı bir blog yazısı taslağı oluşturun. Taslağın sadece :maxsubtopics konusu olmalı. Taslağın bir :tone tonlaması olmalı. Taslağın sonunda bir sonuç konusu olmalı. Üçüncü seviye konular içermemeli. Alfabetik harflerle belirtilen alt konular içinde iç konular eklemeyin, örneğin: \n\nİyi çıktı:\nI. Ana Konu \n A. Alt Konu 1 \n B. Alt Konu 2 \n C. Alt Konu 3 \n\nKötü çıktı:\nI. Ana Konu \nA. Alt Konu 1 \nB. Alt Konu 2\n   1. İç Konu 1\n   2. İç Konu 2\nC. Alt Konu 3\n\n\n Taslak, aşağıdaki metne dayanmalıdır: \n\n:context",
    'write_social_media_post' => ":platform için bir sosyal medya gönderisi yazın. Yazınızı yönlendirmek için aşağıdaki talimatları kullanın:\n\n",
    'write_title' => "Aşağıdaki metin için en fazla 7 kelimeden oluşan bir başlık yazın: \n\n:context\n\n\nİyi ve kötü çıktı örnekleri:\n\nKötü çıktı:\nBaşlık: Bu başlık\n\nİyi çıktı:\nBu başlık\n\nEk talimatlar:\n\n- Başlığın bir :tone tonlaması olmalıdır.\n",
];
