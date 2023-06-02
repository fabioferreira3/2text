<?php

return [
    'blog_first_pass' => "Escreva um artigo de blog, utilizando um tom :tone, utilizando as tags <p> para cercar parágrafos, as tags <h2> para cercar os tópicos principais e as tags <h3> para cercar os tópicos internos, com base no seguinte esboço: \n\n:outline\n\n\nInstruções adicionais: Não envolva as tags h2 e h3 com tags p, por exemplo: \n\n Saída incorreta:\n<p><h2>Tópico</h2></p>\n\nSaída incorreta:\n<p><h2>Tópico</h2><h3>Subtópico</h3></p>\n\n\nA estrutura do esboço deve ser convertida para tags html assim:\n\nEntrada:\nA. Tópico 1\n\nSaída:<h3>A. Tópico 1</h3>\n\nEntrada:\nB. Tópico 2\n\nSaída:<h3>B. Tópico 2</h3>",
    'expand_text' => "Usando um tom :tone, e usando as tags <h3> para subtemas e <p> para parágrafos, expanda sobre: \n\n:context\n\n\nInstruções adicionais:\n-Quando expandir o texto, não crie novos <h3> subtemas. Em vez disso, aumente o número de parágrafos.",
    'given_following_text' => "Dado o seguinte texto:\n\n:text",
    'given_following_context' => "E dado o seguinte contexto:\n\n:context\n\n\n",
    'keyword_instructions' => "- Use a palavra-chave \":keyword\".\n",
    'more_instructions' => "- Siga essas outras instruções na criação do post:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- A meta descrição deve ser baseada no seguinte contexto:\n\n\n :context\n\n\n",
    'post_context_instructions' => "- O post deve ser baseado no seguinte contexto:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- O post deve ter um tom :tone.\n",
    'simplify_text' => "Simplifique o seguinte texto:\n\n:text",
    'summarize_text' => "Resuma o seguinte texto:\n\n :text",
    'write_meta_description' => "Escreva uma meta descrição com um máximo de 20 palavras. Use as seguintes instruções para guiar sua escrita:\n\n",
    'write_outline' => "Crie um esboço de post de blog detalhado e abrangente, com um máximo de dois níveis, usando algarismos romanos para indicar os tópicos principais e letras do alfabeto para indicar os subtópicos. O esboço deve ter apenas :maxsubtopics tópicos. O esboço deve ter um tom :tone. O esboço deve ter um tópico de conclusão no final. Não aninhe um terceiro nível de tópicos. Não adicione tópicos internos dentro dos subtópicos indicados por letras do alfabeto, por exemplo: \n\nBoa saída:\nI. Tópico Principal \n A. Subtópico 1 \n B. Subtópico 2 \n C. Subtópico 3 \n\nSaída ruim:\nI. Tópico Principal \nA. Subtópico 1 \nB. Subtópico 2\n   1. Tópico interno 1\n   2. Tópico interno 2\nC. Subtópico 3\n\n\n O esboço deve ser baseado no seguinte texto: \n\n:context",
    'write_social_media_post' => "Escreva um post para as redes sociais para :platform. Use as seguintes instruções para guiar sua escrita:\n\n",
    'write_title' => "Escreva um título, com um máximo de 7 palavras, para o seguinte texto: \n\n:context\n\n\nExemplos de saídas boas e ruins:\n\nSaída ruim:\nTítulo: Este é o título\n\nBoa saída:\nEste é o título\n\nInstruções adicionais:\n\n- O título deve ter um tom :tone.\n",
];
