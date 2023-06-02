<?php

return [
    'blog_first_pass' => "Scrivi un articolo di blog, utilizzando un tono :tone, utilizza i tag <p> per delimitare i paragrafi, i tag <h2> per delimitare i principali argomenti e i tag <h3> per delimitare gli argomenti interni, basandoti sul seguente schema: \n\n:outline\n\n\nUlteriori istruzioni: Non delimitare i tag h2 e h3 con i tag p, per esempio: \n\n Cattiva uscita:\n<p><h2>Argomento</h2></p>\n\nCattiva uscita:\n<p><h2>Argomento</h2><h3>Argomento interno</h3></p>\n\n\nLa struttura dello schema dovrebbe essere convertita in tag html in questo modo:\n\nInput:\nA. Argomento 1\n\nOutput:<h3>A. Argomento 1</h3>\n\nInput:\nB. Argomento 2\n\nOutput:<h3>B. Argomento 2</h3>",
    'expand_text' => "Usando un tono :tone, e utilizzando i tag <h3> per gli argomenti secondari e i tag <p> per i paragrafi, espandi: \n\n:context\n\n\nUlteriori istruzioni:\n-Quando espandi il testo, non creare nuovi argomenti interni <h3>. Invece, aumenta il numero di paragrafi.",
    'given_following_text' => "Dato il seguente testo:\n\n:text",
    'given_following_context' => "E dato il seguente contesto:\n\n:context\n\n\n",
    'keyword_instructions' => "- Usa la parola chiave \":keyword\".\n",
    'more_instructions' => "- Segui queste altre istruzioni sulla creazione del post:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- La descrizione meta deve essere basata sul seguente contesto:\n\n\n :context\n\n\n",
    'post_context_instructions' => "- Il post deve essere basato sul seguente contesto:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- Il post deve avere un tono :tone.\n",
    'simplify_text' => "Semplifica il seguente testo:\n\n:text",
    'summarize_text' => "Riassumi il seguente testo:\n\n :text",
    'write_meta_description' => "Scrivi una descrizione meta di un massimo di 20 parole. Usa le seguenti istruzioni per guidare la tua scrittura:\n\n",
    'write_outline' => "Crea uno schema di post del blog dettagliato e completo, con un massimo di due livelli, utilizzando i numeri romani per indicare i principali argomenti e le lettere dell'alfabeto per indicare gli argomenti secondari. Lo schema deve avere solo :maxsubtopics argomenti. Lo schema deve avere un tono :tone. Lo schema deve avere un argomento conclusivo alla fine. Non nidificare un terzo livello di argomenti. Non aggiungere argomenti interni all'interno degli argomenti secondari indicati con le lettere dell'alfabeto, per esempio: \n\nBuon output:\nI. Argomento Principale \n A. Argomento Secondario 1 \n B. Argomento Secondario 2 \n C. Argomento Secondario 3 \n\nCattivo output:\nI. Argomento Principale \nA. Argomento Secondario 1 \nB. Argomento Secondario 2\n   1. Argomento Interno 1\n   2. Argomento Interno 2\nC. Argomento Secondario 3\n\n\n Lo schema dovrebbe essere basato sul seguente testo: \n\n:context",
    'write_social_media_post' => "Scrivi un post per i social media per :platform. Usa le seguenti istruzioni per guidare la tua scrittura:\n\n",
    'write_title' => "Scrivi un titolo, con un massimo di 7 parole, per il seguente testo: \n\n:context\n\n\nEsempi di buone e cattive uscite:\n\nCattiva uscita:\nTitolo: Questo è il titolo\n\nBuona uscita:\nQuesto è il titolo\n\nUlteriori istruzioni:\n\n- Il titolo deve avere un tono :tone.",
];
