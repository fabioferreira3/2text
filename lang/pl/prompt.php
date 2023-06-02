<?php

return [
    'blog_first_pass' => "Napisz artykuł na blogu, używając tonacji :tone, używając tagów <p> do otoczenia akapitów, tagów <h2> do otoczenia głównych tematów i tagów <h3> do otoczenia tematów wewnętrznych, oparte na następującym zarysie: \n\n:outline\n\n\nDodatkowe instrukcje: Nie otaczaj tagów h2 i h3 tagami p, na przykład: \n\n Złe wyjście:\n<p><h2>Temat</h2></p>\n\nZłe wyjście:\n<p><h2>Temat</h2><h3>Wewnętrzny temat</h3></p>\n\n\nStruktura zarysu powinna być przetworzona na tagi HTML w ten sposób:\n\nWejście:\nA. Temat 1\n\nWyjście:<h3>A. Temat 1</h3>\n\nWejście:\nB. Temat 2\n\nWyjście:<h3>B. Temat 2</h3>",
    'expand_text' => "Korzystając z tonacji :tone, i używając tagów <h3> dla podtematów i tagów <p> dla akapitów, rozwiń: \n\n:context\n\n\nDalsze instrukcje:\n-Podczas rozszerzania tekstu nie twórz nowych <h3> wewnętrznych tematów. Zamiast tego zwiększ liczbę akapitów.",
    'given_following_text' => "Biorąc pod uwagę następujący tekst:\n\n:text",
    'given_following_context' => "I biorąc pod uwagę następujący kontekst:\n\n:context\n\n\n",
    'keyword_instructions' => "- Użyj słowa kluczowego \":keyword\".\n",
    'more_instructions' => "- Postępuj zgodnie z tymi innymi instrukcjami dotyczącymi tworzenia postów:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- Opis meta musi być oparty na następującym kontekście:\n\n\n :context\n\n\n",
    'post_context_instructions' => "- Post musi być oparty na następującym kontekście:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- Post musi mieć tonację :tone.\n",
    'simplify_text' => "Uprość następujący tekst:\n\n:text",
    'summarize_text' => "Podsumuj następujący tekst:\n\n :text",
    'write_meta_description' => "Napisz opis meta o maksymalnej liczbie 20 słów. Skorzystaj z poniższych instrukcji, aby pokierować swoje pisanie:\n\n",
    'write_outline' => "Stwórz dogłębny i kompleksowy zarys wpisu na blogu, o maksymalnym dwóch poziomach, używając rzymskich cyfr do oznaczania głównych tematów i liter alfabetu do oznaczania podtematów. Zarys musi mieć tylko :maxsubtopics tematów. Zarys musi mieć tonację :tone. Zarys musi mieć temat końcowy na końcu. Nie zawieraj trzeciego poziomu tematów. Nie dodawaj tematów wewnętrznych do podtematów oznaczonych literami alfabetu, na przykład: \n\nDobre wyjście:\nI. Główny temat \n A. Podtemat 1 \n B. Podtemat 2 \n C. Podtemat 3 \n\nZłe wyjście:\nI. Główny temat \nA. Podtemat 1 \nB. Podtemat 2\n   1. Wewnętrzny temat 1\n   2. Wewnętrzny temat 2\nC. Podtemat 3\n\n\n Zarys powinien być oparty na następującym tekście: \n\n:context",
    'write_social_media_post' => "Napisz post na :platform. Skorzystaj z poniższych instrukcji, aby pokierować swoje pisanie:\n\n",
    'write_title' => "Napisz tytuł o maksymalnej liczbie 7 słów dla następującego tekstu: \n\n:context\n\n\nPrzykłady dobrych i złych wyjść:\n\nZłe wyjście:\nTytuł: To jest tytuł\n\nDobre wyjście:\nTo jest tytuł\n\nDalsze instrukcje:\n\n- Tytuł musi mieć tonację :tone.\n",
];
