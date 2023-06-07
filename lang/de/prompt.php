<?php

return [
    'blog_first_pass' => "Verfassen Sie einen Blog-Artikel im :tone Ton, verwenden Sie <p> Tags, um Absätze zu umschließen, <h2> Tags, um Hauptthemen zu umschließen, und <h3> Tags, um Unterpunkte zu umschließen, basierend auf der folgenden Gliederung: \n\n:outline\n\n\nWeitere Anweisungen: Umgeben Sie h2 und h3 Tags nicht mit p Tags, zum Beispiel: \n\n Schlechter Ausgang:\n<p><h2>Thema</h2></p>\n\nSchlechter Ausgang:\n<p><h2>Thema</h2><h3>Unterthema</h3></p>\n\n\nDie Gliederungsstruktur sollte zu HTML-Tags wie folgt verarbeitet werden:\n\nEingabe:\nA. Thema 1\n\nAusgabe:<h3>A. Thema 1</h3>\n\nEingabe:\nB. Thema 2\n\nAusgabe:<h3>B. Thema 2</h3>",
    'expand_text' => "Mit einem :tone Ton und mit <h3> Tags für Unterthemen und <p> Tags für Absätze, erweitern Sie: \n\n:context\n\n\nWeitere Anweisungen:\n-Wenn Sie den Text erweitern, erstellen Sie keine neuen <h3> Unterthemen. Stattdessen erhöhen Sie die Anzahl der Absätze.",
    'given_following_text' => "Angesichts des folgenden Textes:\n\n:text",
    'given_following_context' => "Und angesichts des folgenden Kontexts:\n\n:context\n\n\n",
    'keyword_instructions' => "- Verwenden Sie das Schlüsselwort \":keyword\".\n",
    'max_words' => "- Der Text darf maximal :max Wörter haben\n",
    'more_instructions' => "- Befolgen Sie diese weiteren Anweisungen zur Erstellung des Beitrags:\n\n\n :instructions\n\n\n",
    'meta_description_context_instructions' => "- Die Meta-Beschreibung muss auf dem folgenden Kontext basieren:\n\n\n :context\n\n\n",
    'post_context_instructions' => "- Der Beitrag muss auf dem folgenden Kontext basieren:\n\n\n :context\n\n\n",
    'post_tone_instructions' => "- Der Beitrag muss einen :tone Ton haben.\n",
    'simplify_text' => "Vereinfachen Sie den folgenden Text:\n\n:text",
    'style_instructions' => "- Verwenden Sie einen :style Schreibstil\n",
    'summarize_text' => "Fassen Sie den folgenden Text zusammen:\n\n :text",
    'tone_instructions' => "- Verwenden Sie einen :tone Ton",
    'write_meta_description' => "Schreiben Sie eine Meta-Beschreibung von maximal 20 Wörtern. Verwenden Sie die folgenden Anweisungen, um Ihr Schreiben zu leiten:\n\n",
    'write_outline' => "Erstellen Sie eine gründliche und umfassende Blog-Beitragsgliederung mit höchstens zwei Ebenen, indem Sie römische Ziffern zur Kennzeichnung von Hauptthemen und Buchstaben zur Kennzeichnung von Unterthemen verwenden. Die Gliederung darf nur :maxsubtopics Themen haben. Die Gliederung muss einen :tone Ton haben. Die Gliederung muss am Ende ein abschließendes Thema haben. Verschachteln Sie keine dritte Ebene von Themen. Fügen Sie keine Unterthemen innerhalb der mit Buchstaben gekennzeichneten Unterthemen hinzu, zum Beispiel: \n\nGute Ausgabe:\nI. Hauptthema \n A. Unterthema 1 \n B. Unterthema 2 \n C. Unterthema 3 \n\nSchlechte Ausgabe:\nI. Hauptthema \nA. Unterthema 1 \nB. Unterthema 2\n   1. Inneres Thema 1\n   2. Inneres Thema 2\nC. Unterthema 3\n\n\n Die Gliederung sollte auf dem folgenden Text basieren: \n\n:context",
    'write_social_media_post' => "Schreiben Sie einen Social-Media-Beitrag für :platform. Verwenden Sie die folgenden Anweisungen, um Ihr Schreiben zu leiten:\n\n",
    'write_title' => "Schreiben Sie einen Titel mit höchstens 7 Wörtern für den folgenden Text: \n\n:context\n\n\nBeispiele für gute und schlechte Ausgaben:\n\nSchlechte Ausgabe:\nTitel: Dies ist der Titel\n\nGute Ausgabe:\nDies ist der Titel\n\nWeitere Anweisungen:\n\n- Der Titel muss einen :tone Ton haben.",
];
