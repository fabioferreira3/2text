<?php

return [
    'blog_first_pass' => "Escribe un artículo de blog, utilizando un tono :tone, usando las etiquetas <p> para rodear los párrafos, etiquetas <h2> para rodear los temas principales y etiquetas <h3> para rodear los temas internos, basándote en el siguiente esquema: \n\n:outline\n\n\nInstrucciones adicionales: No rodees las etiquetas h2 y h3 con etiquetas p, por ejemplo: \n\nSalida incorrecta:\n<p><h2>Tema</h2></p>\n\nSalida incorrecta:\n<p><h2>Tema</h2><h3>Subtema</h3></p>\n\n\nLa estructura del esquema debería ser parseada a etiquetas html de la siguiente manera:\n\nEntrada:\nA. Tema 1\n\nSalida:<h3>A. Tema 1</h3>\n\nEntrada:\nB. Tema 2\n\nSalida:<h3>B. Tema 2</h3>",
    'expand_text' => "Utilizando un tono :tone, y usando etiquetas <h3> para los subtemas y etiquetas <p> para los párrafos, amplía: \n\n:context\n\n\nInstrucciones adicionales:\n- Al expandir el texto, no crees nuevos <h3> subtemas. En su lugar, incrementa el número de párrafos.",
    'given_following_text' => "Dado el siguiente texto:\n\n:text",
    'given_following_context' => "Y dado el siguiente contexto:\n\n:context\n\n\n",
    'keyword_instructions' => "- Usa la palabra clave \":keyword\".\n",
    'max_words' => "- El texto debe tener un máximo de :max palabras\n",
    'more_instructions' => "- Sigue estas otras instrucciones para la creación del post:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- La descripción meta debe basarse en el siguiente contexto:\n\n\n :context\n\n\n",
    'post_context_instructions' => "- El post debe basarse en el siguiente contexto:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- El post debe tener un tono :tone.\n",
    'simplify_text' => "Simplifica el siguiente texto:\n\n:text",
    'style_instructions' => "- Usa un estilo de escritura :style\n",
    'summarize_text' => "Resume el siguiente texto:\n\n :text",
    'tone_instructions' => "- Usa un tono :tone",
    'write_meta_description' => "Escribe una descripción meta con un máximo de 20 palabras. Utiliza las siguientes instrucciones para guiar tu escritura:\n\n",
    'write_outline' => "Crea un esquema de post de blog detallado y completo, con un máximo de dos niveles, utilizando números romanos para indicar los temas principales y letras del alfabeto para indicar los subtemas. El esquema debe tener solo :maxsubtopics temas. El esquema debe tener un tono :tone. El esquema debe tener un tema de conclusión al final. No anides un tercer nivel de temas. No añadas subtemas dentro de los subtemas indicados por las letras del alfabeto, por ejemplo: \n\nBuena salida:\nI. Tema Principal \n A. Subtema 1 \n B. Subtema 2 \n C. Subtema 3 \n\nMala salida:\nI. Tema Principal \nA. Subtema 1 \nB. Subtema 2\n   1. Subtema interno 1\n   2. Subtema interno 2\nC. Subtema 3\n\n\n El esquema debe basarse en el siguiente texto: \n\n:context",
    'write_social_media_post' => "Escribe una publicación para redes sociales para :platform. Utiliza las siguientes instrucciones para guiar tu escritura:\n\n",
    'write_title' => "Escribe un título, con un máximo de 7 palabras, para el siguiente texto: \n\n:context\n\n\nEjemplos de salidas buenas y malas:\n\nMala salida:\nTítulo: Este es el título\n\nBuena salida:\nEste es el título\n\nInstrucciones adicionales:\n\n- El título debe tener un tono :tone.\n",
];
