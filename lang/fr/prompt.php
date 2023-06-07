<?php

return [
    'blog_first_pass' => "Rédigez un article de blog, en utilisant un ton :tone, avec des balises <p> pour entourer les paragraphes, des balises <h2> pour les principaux sujets et des balises <h3> pour les sous-sujets, en se basant sur le plan suivant: \n\n:outline\n\n\nInstructions supplémentaires: N'entourez pas les balises h2 et h3 avec des balises p, par exemple: \n\n Mauvaise sortie:\n<p><h2>Sujet</h2></p>\n\nMauvaise sortie:\n<p><h2>Sujet</h2><h3>Sous-sujet</h3></p>\n\n\nLa structure du plan doit être convertie en balises HTML comme suit:\n\nEntrée:\nA. Sujet 1\n\nSortie:<h3>A. Sujet 1</h3>\n\nEntrée:\nB. Sujet 2\n\nSortie:<h3>B. Sujet 2</h3>",
    'expand_text' => "Utilisant un ton :tone, et des balises <h3> pour les sous-sujets et <p> pour les paragraphes, développez: \n\n:context\n\n\nInstructions supplémentaires:\n-Ne créez pas de nouveaux <h3> sous-sujets. Au lieu de cela, augmentez le nombre de paragraphes.",
    'given_following_text' => "Étant donné le texte suivant:\n\n:text",
    'given_following_context' => "Et étant donné le contexte suivant:\n\n:context\n\n\n",
    'keyword_instructions' => "- Utilisez le mot-clé \":keyword\".\n",
    'max_words' => "- Le texte doit avoir un maximum de :max mots\n",
    'more_instructions' => "- Suivez ces autres instructions sur la création de l'article:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- La meta description doit être basée sur le contexte suivant:\n\n\n :context\n\n\n",
    'post_context_instructions' => "- L'article doit être basé sur le contexte suivant:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- L'article doit avoir un ton :tone.\n",
    'simplify_text' => "Simplifiez le texte suivant:\n\n:text",
    'style_instructions' => "- Utilisez un style d'écriture :style\n",
    'summarize_text' => "Résumez le texte suivant:\n\n :text",
    'tone_instructions' => "- Utilisez un ton :tone",
    'write_meta_description' => "Rédigez une meta description d'un maximum de 20 mots. Utilisez les instructions suivantes pour guider votre écriture:\n\n",
    'write_outline' => "Créez un plan de blog post détaillé et complet, avec un maximum de deux niveaux, en utilisant des chiffres romains pour indiquer les sujets principaux et des lettres alphabétiques pour indiquer les sous-sujets. Le plan ne doit avoir que :maxsubtopics sujets. Le plan doit avoir un ton :tone. Le plan doit avoir un sujet de conclusion à la fin. Ne pas imbriquer un troisième niveau de sujets. Ne pas ajouter de sous-sujets à l'intérieur des sous-sujets indiqués par des lettres alphabétiques, par exemple: \n\nBon résultat:\nI. Sujet principal \n A. Sous-sujet 1 \n B. Sous-sujet 2 \n C. Sous-sujet 3 \n\nMauvais résultat:\nI. Sujet principal \nA. Sous-sujet 1 \nB. Sous-sujet 2\n   1. Sous-sujet interne 1\n   2. Sous-sujet interne 2\nC. Sous-sujet 3\n\n\n Le plan doit être basé sur le texte suivant: \n\n:context",
    'write_social_media_post' => "Rédigez un post pour les réseaux sociaux pour :platform. Utilisez les instructions suivantes pour guider votre écriture:\n\n",
    'write_title' => "Rédigez un titre, avec un maximum de 7 mots, pour le texte suivant: \n\n:context\n\n\nExemples de bonnes et mauvaises sorties:\n\nMauvaise sortie:\nTitre: Voici le titre\n\nBonne sortie:\nVoici le titre\n\nInstructions supplémentaires:\n\n- Le titre doit avoir un ton :tone.",
];
