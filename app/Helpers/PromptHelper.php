<?php

namespace App\Helpers;

use App\Enums\Tone;

class PromptHelper
{
    protected string $language;

    public function __construct($language = 'en')
    {
        $this->language = $language;
    }

    public function summarize($text)
    {
        switch ($this->language) {
            case 'en':
                return "Summarize the following text: \n\n" . $text;
            case 'pt':
                return "Resuma o seguinte texto: \n\n" . $text;
            case 'es':
                return "Resuma el siguiente texto: \n\n" . $text;
            case 'fr':
                return "Résumez le texte suivant: \n\n" . $text;
            case 'de':
                return "Fassen Sie den folgenden Text zusammen: \n\n" . $text;
            case 'it':
                return "Riassumi il seguente testo: \n\n" . $text;
            case 'ru':
                return "Резюме следующий текст: \n\n" . $text;
            case 'ja':
                return "次のテキストを要約する: \n\n" . $text;
            case 'ch':
                return "总结以下文本：\n\n" . $text;
            case 'ar':
                return "لخص النص التالي: \n\n" . $text;
            case 'ko':
                return "다음 텍스트를 요약하십시오. \n\n" . $text;
            case 'tr':
                return "Aşağıdaki metni özetleyin: \n\n" . $text;
            case 'pl':
                return "Przedstaw następujący tekst: \n\n" . $text;
            case 'el':
                return "Συνοψίστε τον ακόλουθο κείμενο: \n\n" . $text;
            default:
                return '';
        }
    }

    public function simplify($text)
    {
        switch ($this->language) {
            case 'en':
                return "Simplify the following text: \n\n" . $text;
            case 'pt':
                return "Simplifique o seguinte texto: \n\n" . $text;
            case 'es':
                return "Simplifique el siguiente texto: \n\n" . $text;
            case 'fr':
                return "Simplifiez le texte suivant: \n\n" . $text;
            case 'de':
                return "Vereinfachen Sie den folgenden Text: \n\n" . $text;
            case 'it':
                return "Semplifica il seguente testo: \n\n" . $text;
            case 'ru':
                return "Упростите следующий текст: \n\n" . $text;
            case 'ja':
                return "次のテキストを簡素化する: \n\n" . $text;
            case 'ch':
                return "简化以下文本：\n\n" . $text;
            case 'ar':
                return "بسط النص التالي: \n\n" . $text;
            case 'ko':
                return "다음 텍스트를 단순화하십시오. \n\n" . $text;
            case 'tr':
                return "Aşağıdaki metni basitleştirin: \n\n" . $text;
            case 'pl':
                return "Uprość następujący tekst: \n\n" . $text;
            case 'el':
                return "Απλοποιήστε τον ακόλουθο κείμενο: \n\n" . $text;
            default:
                return '';
        }
    }

    public function writeFirstPass($outline, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                return "Write a blog article, using a " . $tone . " tone, using <p> tags to surround paragraphs, <h2> tags to surround main topics and <h3> tags to surround inner topics, based on the following outline: \n\n" . $outline . "\n\n\nFurther instructions: Do not surround h2 and h3 tags with p tags, for example: \n\n Bad output:\n<p><h2>Topic</h2></p>\n\nBad output:\n<p><h2>Topic</h2><h3>Inner topic</h3></p>\n\n\nThe outline structure should be parsed to html tags like this:\n\nInput:\nA. Topic 1\n\nOutput:<h3>A. Topic 1</h3>\n\nInput:\nB. Topic 2\n\nOutput:<h3>B. Topic 2</h3>";
            case 'pt':
                return "Escreva um artigo para um blog, usando um tom " . $tone . ", usando tags <p> ao redor dos parágrafos, tags <h2> ao redor dos tópicos principais e tags <h3> ao redor dos tópicos internos, baseado no seguinte esboço: \n\n" . $outline . "\n\n\nOutras instruções: Não coloque tags p dentro de tags h2 e h3, por exemplo: \n\n Output incorreto:\n<p><h2>Tópico</h2></p>\n\nOutput incorreto:\n<p><h2>Tópico</h2><h3>Tópico interno</h3></p>\n\n\nA estrutura do esboço deve ser convertida em tags html desta forma::\n\nInput:\nA. Tópico 1\n\nOutput:<h3>A. Tópico 1</h3>\n\nInput:\nB. Tópico 2\n\nOutput:<h3>B. Tópico 2</h3>";
            case 'es':
                return "Escribe un artículo para un blog, usando un tono " . $tone . ", usando tags <p> alrededor de los párrafos, tags <h2> alrededor de los temas principales y tags <h3> alrededor de los temas internos, basado en el siguiente esquema: \n\n" . $outline . "\n\n\nOtras instrucciones: No coloque tags p dentro de tags h2 y h3, por ejemplo: \n\n Output incorrecto:\n<p><h2>Tema</h2></p>\n\nOutput incorrecto:\n<p><h2>Tema</h2><h3>Tema interno</h3></p>\n\n\nLa estructura del esquema debe convertirse en tags html de esta forma::\n\nInput:\nA. Tema 1\n\nOutput:<h3>A. Tema 1</h3>\n\nInput:\nB. Tema 2\n\nOutput:<h3>B. Tema 2</h3>";
            case 'fr':
                return "Écrivez un article de blog, en utilisant un ton " . $tone . ", en utilisant des tags <p> pour entourer les paragraphes, des tags <h2> pour entourer les sujets principaux et des tags <h3> pour entourer les sujets internes, basé sur le plan suivant: \n\n" . $outline . "\n\n\nAutres instructions: Ne pas entourer les tags h2 et h3 avec des tags p, par exemple: \n\n Mauvais output:\n<p><h2>Sujet</h2></p>\n\nMauvais output:\n<p><h2>Sujet</h2><h3>Sujet interne</h3></p>\n\n\nLa structure du plan doit être analysée en tags html comme ceci:\n\nInput:\nA. Sujet 1\n\nOutput:<h3>A. Sujet 1</h3>\n\nInput:\nB. Sujet 2\n\nOutput:<h3>B. Sujet 2</h3>";
            case 'de':
                return "Schreiben Sie einen Blog-Artikel, mit einem " . $tone . " Ton, mit <p> Tags um Absätze, <h2> Tags um Hauptthemen und <h3> Tags um Untertopics, basierend auf dem folgenden Entwurf: \n\n" . $outline . "\n\n\nWeitere Anweisungen: Umgeben Sie h2 und h3 Tags nicht mit p Tags, zum Beispiel: \n\n Schlechtes Ergebnis:\n<p><h2>Thema</h2></p>\n\nSchlechtes Ergebnis:\n<p><h2>Thema</h2><h3>Inneres Thema</h3></p>\n\n\nDie Gliederungsstruktur sollte in html Tags wie folgt geparst werden:\n\nInput:\nA. Thema 1\n\nOutput:<h3>A. Thema 1</h3>\n\nInput:\nB. Thema 2\n\nOutput:<h3>B. Thema 2</h3>";
            case 'it':
                return "Scrivi un articolo per un blog, usando un tono " . $tone . ", usando i tag <p> per circondare i paragrafi, i tag <h2> per circondare gli argomenti principali e i tag <h3> per circondare gli argomenti interni, basato sul seguente schema: \n\n" . $outline . "\n\n\nAltre istruzioni: Non circondare i tag h2 e h3 con i tag p, per esempio: \n\n Output errato:\n<p><h2>Argomento</h2></p>\n\nOutput errato:\n<p><h2>Argomento</h2><h3>Argomento interno</h3></p>\n\n\nLa struttura dello schema dovrebbe essere analizzata in tag html come questo:\n\nInput:\nA. Argomento 1\n\nOutput:<h3>A. Argomento 1</h3>\n\nInput:\nB. Argomento 2\n\nOutput:<h3>B. Argomento 2</h3>";
            case 'ru':
                return "Напишите статью для блога, используя " . $tone . " тон, используя теги <p> для обрамления абзацев, теги <h2> для обрамления основных тем и теги <h3> для обрамления внутренних тем, основываясь на следующем плане: \n\n" . $outline . "\n\n\nДругие инструкции: Не обрамляйте теги h2 и h3 тегами p, например: \n\n Неправильный вывод:\n<p><h2>Тема</h2></p>\n\nНеправильный вывод:\n<p><h2>Тема</h2><h3>Внутренняя тема</h3></p>\n\n\nСтруктура плана должна быть разобрана в html теги таким образом:\n\nInput:\nA. Тема 1\n\nOutput:<h3>A. Тема 1</h3>\n\nInput:\nB. Тема 2\n\nOutput:<h3>B. Тема 2</h3>";
            case 'ja':
                return "ブログ記事を書いてください。" . $tone . " トーンを使用し、段落を囲むために <p> タグ、メイントピックを囲むために <h2> タグ、サブトピックを囲むために <h3> タグを使用して、次のアウトラインに基づいてください: \n\n" . $outline . "\n\n\nその他の指示: h2 と h3 タグを p タグで囲まないでください。例えば: \n\n 間違ったアウトプット:\n<p><h2>トピック</h2></p>\n\n間違ったアウトプット:\n<p><h2>トピック</h2><h3>サブトピック</h3></p>\n\n\nアウトラインの構造は、次のように html タグで解析される必要があります:\n\nInput:\nA. トピック 1\n\nOutput:<h3>A. トピック 1</h3>\n\nInput:\nB. トピック 2\n\nOutput:<h3>B. トピック 2</h3>";
            case 'ch':
                return "请写一篇博客文章，使用" . $tone . "语气，使用 <p> 标签围绕段落，使用 <h2> 标签围绕主题，使用 <h3> 标签围绕子主题，基于以下大纲: \n\n" . $outline . "\n\n\n其他说明: 不要用 p 标签围绕 h2 和 h3 标签，例如: \n\n 错误的输出:\n<p><h2>主题</h2></p>\n\n错误的输出:\n<p><h2>主题</h2><h3>子主题</h3></p>\n\n\n大纲结构应该被解析为 html 标签，如下所示:\n\nInput:\nA. 主题 1\n\nOutput:<h3>A. 主题 1</h3>\n\nInput:\nB. 主题 2\n\nOutput:<h3>B. 主题 2</h3>";
            case 'ar':
                return "اكتب مقالة لمدونة، باستخدام " . $tone . " النبرة، باستخدام علامات <p> لتحيط الفقرات، وعلامات <h2> لتحيط المواضيع الرئيسية، وعلامات <h3> لتحيط المواضيع الفرعية، استنادا إلى المخطط التالي: \n\n" . $outline . "\n\n\nتعليمات أخرى: لا تحيط بعلامات h2 و h3 بعلامات p، على سبيل المثال: \n\n إخراج خاطئ:\n<p><h2>موضوع</h2></p>\n\nإخراج خاطئ:\n<p><h2>موضوع</h2><h3>موضوع فرعي</h3></p>\n\n\nيجب تحليل هيكل المخطط إلى علامات html على النحو التالي:\n\nInput:\nA. موضوع 1\n\nOutput:<h3>A. موضوع 1</h3>\n\nInput:\nB. موضوع 2\n\nOutput:<h3>B. موضوع 2</h3>";
            case 'ko':
                return "블로그 글을 작성하십시오. " . $tone . " 톤을 사용하고, 단락을 둘러싸기 위해 <p> 태그를 사용하고, 주제를 둘러싸기 위해 <h2> 태그를 사용하고, 하위 주제를 둘러싸기 위해 <h3> 태그를 사용하십시오. 다음 아웃라인을 기반으로 하십시오: \n\n" . $outline . "\n\n\n기타 지침: p 태그로 h2 및 h3 태그를 둘러싸지 마십시오. 예를 들어: \n\n 잘못된 출력:\n<p><h2>주제</h2></p>\n\n잘못된 출력:\n<p><h2>주제</h2><h3>하위 주제</h3></p>\n\n\n아웃라인 구조는 다음과 같이 html 태그로 분석되어야 합니다:\n\nInput:\nA. 주제 1\n\nOutput:<h3>A. 주제 1</h3>\n\nInput:\nB. 주제 2\n\nOutput:<h3>B. 주제 2</h3>";
            case 'tr':
                return "Bir blog yazısı yazın, " . $tone . " tonunu kullanın, paragrafları <p> etiketi ile çevreleyin, ana konuları <h2> etiketi ile çevreleyin, alt konuları <h3> etiketi ile çevreleyin, aşağıdaki taslak üzerine dayanarak: \n\n" . $outline . "\n\n\nDiğer talimatlar: p etiketi ile h2 ve h3 etiketlerini çevrelemeyin, örneğin: \n\n Yanlış çıktı:\n<p><h2>Konu</h2></p>\n\nYanlış çıktı:\n<p><h2>Konu</h2><h3>Alt Konu</h3></p>\n\n\nTaslak yapısı aşağıdaki gibi html etiketlerine ayrıştırılmalıdır:\n\nInput:\nA. Konu 1\n\nOutput:<h3>A. Konu 1</h3>\n\nInput:\nB. Konu 2\n\nOutput:<h3>B. Konu 2</h3>";
            case 'pl':
                return "Napisz artykuł na bloga, używając tonu " . $tone . ", otaczając akapity znacznikami <p>, otaczając tematy główne znacznikami <h2>, otaczając tematy podrzędne znacznikami <h3>, na podstawie następującego szkicu: \n\n" . $outline . "\n\n\nInne wskazówki: Nie otaczaj znacznikami p znaczników h2 i h3, na przykład: \n\n Niepoprawny wynik:\n<p><h2>Temat</h2></p>\n\nNiepoprawny wynik:\n<p><h2>Temat</h2><h3>Temat podrzędny</h3></p>\n\n\nStruktura szkicu powinna być analizowana na znaczniki html, jak pokazano poniżej:\n\nInput:\nA. Temat 1\n\nOutput:<h3>A. Temat 1</h3>\n\nInput:\nB. Temat 2\n\nOutput:<h3>B. Temat 2</h3>";
            case 'el':
                return "Γράψτε ένα άρθρο για ιστολόγιο, χρησιμοποιώντας τον τόνο " . $tone . ", περικλείοντας τις παραγράφους με τις ετικέτες <p>, περικλείοντας τα κύρια θέματα με τις ετικέτες <h2>, περικλείοντας τα υπο-θέματα με τις ετικέτες <h3>, με βάση το παρακάτω σχέδιο: \n\n" . $outline . "\n\n\nΆλλες οδηγίες: Μην περικλείετε τις ετικέτες h2 και h3 με την ετικέτα p, για παράδειγμα: \n\n Λανθασμένη έξοδος:\n<p><h2>Θέμα</h2></p>\n\nΛανθασμένη έξοδος:\n<p><h2>Θέμα</h2><h3>Υπο-θέμα</h3></p>\n\n\nΗ δομή του σχεδίου πρέπει να αναλύεται σε ετικέτες html, όπως φαίνεται παρακάτω:\n\nInput:\nA. Θέμα 1\n\nOutput:<h3>A. Θέμα 1</h3>\n\nInput:\nB. Θέμα 2\n\nOutput:<h3>B. Θέμα 2</h3>";
            default:
                return '';
        }
    }

    public function writeTitle($context, $tone = 'casual', $keyword = null)
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                $withKeyword = $keyword ? ", using the keyword $keyword" : '';
                return "Write a title, with a maximum of 7 words, $withKeyword and with a $tone tone, for the following text: \n\n" . $context . "\n\n\nExamples of good and bad outputs:\n\nBad output:\nTitle: This is the title\n\nGood output:\nThis is the title";
            case 'pt':
                $withKeyword = $keyword ? ", usando a palavra-chave $keyword" : '';
                return "Escreva um título, com no máximo 7 palavras, $withKeyword e com um tom $tone, baseado no seguinte texto: \n\n" . $context . "\n\n\nExemplos de outputs bons e ruins:\n\nOutput ruim:\nTítulo: Este é o título\n\nOutput correto:\nEste é o título";
            case 'es':
                $withKeyword = $keyword ? ", usando la palabra clave $keyword" : '';
                return "Escribe un título, con un máximo de 7 palabras, $withKeyword y con un tono $tone, basado en el siguiente texto: \n\n" . $context . "\n\n\nEjemplos de outputs buenos y malos:\n\nOutput malo:\nTítulo: Este es el título\n\nOutput correcto:\nEste es el título";
            case 'fr':
                $withKeyword = $keyword ? ", en utilisant le mot-clé $keyword" : '';
                return "Écrivez un titre, avec un maximum de 7 mots, $withKeyword et avec un ton $tone, pour le texte suivant: \n\n" . $context . "\n\n\nExemples de bons et mauvais outputs:\n\nMauvais output:\nTitre: C'est le titre\n\nBon output:\nC'est le titre";
            case 'de':
                $withKeyword = $keyword ? ", mit dem Keyword $keyword" : '';
                return "Schreiben Sie einen Titel mit maximal 7 Wörtern, $withKeyword und mit einem $tone Ton für den folgenden Text: \n\n" . $context . "\n\n\nBeispiele für gute und schlechte Ergebnisse:\n\nSchlechtes Ergebnis:\nTitel: Dies ist der Titel\n\nGutes Ergebnis:\nDies ist der Titel";
            case 'it':
                $withKeyword = $keyword ? ", con la parola chiave $keyword" : '';
                return "Scrivi un titolo, con un massimo di 7 parole, $withKeyword e con un tono $tone, per il seguente testo: \n\n" . $context . "\n\n\nEsempi di output buoni e cattivi:\n\nOutput cattivo:\nTitolo: Questo è il titolo\n\nOutput buono:\nQuesto è il titolo";
            case 'ru':
                $withKeyword = $keyword ? ", используя ключевое слово $keyword" : '';
                return "Напишите заголовок, не более 7 слов, $withKeyword и с $tone тональностью, для следующего текста: \n\n" . $context . "\n\n\nПримеры хороших и плохих результатов:\n\nПлохой результат:\nЗаголовок: Это заголовок\n\nХороший результат:\nЭто заголовок";
            case 'ja':
                $withKeyword = $keyword ? "、キーワード $keyword を使用して" : '';
                return "次のテキストに基づいて、7語以内で、$withKeyword $tone トーンでタイトルを書いてください: \n\n" . $context . "\n\n\n良いアウトプットと悪いアウトプットの例:\n\n悪いアウトプット:\nタイトル: これはタイトルです\n\n良いアウトプット:\nこれはタイトルです";
            case 'ch':
                $withKeyword = $keyword ? "，使用关键词 $keyword" : '';
                return "根据以下文本，用不超过7个单词，$withKeyword 和 $tone 语气写一个标题: \n\n" . $context . "\n\n\n好的和坏的输出示例:\n\n坏的输出:\n标题: 这是标题\n\n好的输出:\n这是标题";
            case 'ar':
                $withKeyword = $keyword ? "، باستخدام الكلمة الرئيسية $keyword" : '';
                return "اكتب عنوانًا، بحد أقصى 7 كلمات، $withKeyword وبلهجة $tone للنص التالي: \n\n" . $context . "\n\n\nأمثلة على النتائج الجيدة والسيئة:\n\nنتيجة سيئة:\nالعنوان: هذا هو العنوان\n\nنتيجة جيدة:\nهذا هو العنوان";
            case 'ko':
                $withKeyword = $keyword ? "키워드 $keyword 를 사용하여" : '';
                return "다음 텍스트를 기반으로, 최대 7 단어, $withKeyword $tone 톤으로 제목을 작성하십시오: \n\n" . $context . "\n\n\n좋은 결과와 나쁜 결과의 예:\n\n나쁜 결과:\n제목: 이것은 제목입니다\n\n좋은 결과:\n이것은 제목입니다";
            case 'tr':
                $withKeyword = $keyword ? ", anahtar kelime $keyword kullanarak" : '';
                return "Aşağıdaki metne dayanarak, 7 kelimeyi geçmeyecek şekilde, $withKeyword $tone tonunda bir başlık yazın: \n\n" . $context . "\n\n\nİyi ve kötü çıktı örnekleri:\n\nKötü çıktı:\nBaşlık: Bu başlıktır\n\nİyi çıktı:\nBu başlıktır";
            case 'pl':
                $withKeyword = $keyword ? ", używając słowa kluczowego $keyword" : '';
                return "Napisz tytuł, maksymalnie 7 słów, $withKeyword i z tonem $tone, dla następującego tekstu: \n\n" . $context . "\n\n\nPrzykłady dobrych i złych wyników:\n\nZły wynik:\nTytuł: To jest tytuł\n\nDobry wynik:\nTo jest tytuł";
            case 'el':
                $withKeyword = $keyword ? ", χρησιμοποιώντας τη λέξη-κλειδί $keyword" : '';
                return "Γράψτε έναν τίτλο, με μέγιστο 7 λέξεις, $withKeyword και με έναν τόνο $tone, για τον παρακάτω κείμενο: \n\n" . $context . "\n\n\nΠαραδείγματα καλών και κακών αποτελεσμάτων:\n\nΚακό αποτέλεσμα:\nΤίτλος: Αυτός είναι ο τίτλος\n\nΚαλό αποτέλεσμα:\nΑυτός είναι ο τίτλος";
            default:
                return '';
        }
    }

    public function writeOutline($context, $maxSubtopics, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                return "Create an indept and comprehensive blog post outline, with maximum of two levels, with a " . $tone . " tone, using roman numerals indicating main topics and alphabet letters to indicate subtopics. The outline must have only $maxSubtopics topics. Do not nest a third level of topics. Do not add inner topics inside the subtopics indicated by alphabet letters, for example: \n\nGood output:\nI. Main Topic \n A. Subtopic 1 \n B. Subtopic 2 \n C. Subtopic 3 \n\nBad output:\nI. Main Topic \nA. Subtopic 1 \nB. Subtopic 2\n   1. Inner topic 1\n   2. Inner topic 2\nC. Subtopic 3\n\n\n The outline should be based on the following text: \n\n" . $context;
            case 'pt':
                return "Crie um esboço de postagem de blog aprofundado e abrangente, com no máximo dois níveis, com um tom " . $tone . ", usando números romanos para indicar os tópicos principais e letras do alfabeto comum para indicar subtópicos. O esboço deve ter somente $maxSubtopics tópicos. Não adicione um terceiro nível de tópicos. Não adicione tópicos internos dentro dos subtópicos indicados com letras do alfabeto comum, por exemplo: \n\nOutput correto:\nI. Tópico Principal \n A. Sub-tópico 1 \n B. Sub-tópico 2 \n C. Sub-tópico 3 \n\nOutput errado:\nI. Tópico Principal \nA. Sub-tópico 1 \nB. Sub-tópico 2\n   1. Sub-tópico interno 1\n   2. Sub-tópico interno 2\nC. Sub-tópico 3\n\n\n O esboço deve ser baseado no seguinte texto: \n\n" . $context;
            case 'es':
                return "Crea un esquema de publicación de blog profundo y completo, con un máximo de dos niveles, con un tono " . $tone . ", usando números romanos para indicar los temas principales y letras del alfabeto común para indicar subtemas. El esquema debe tener solamente $maxSubtopics temas. No agregues un tercer nivel de temas. No agregues temas internos dentro de los subtemas indicados con letras del alfabeto común, por ejemplo: \n\nOutput correcto:\nI. Tema Principal \n A. Subtema 1 \n B. Subtema 2 \n C. Subtema 3 \n\nOutput incorrecto:\nI. Tema Principal \nA. Subtema 1 \nB. Subtema 2\n   1. Subtema interno 1\n   2. Subtema interno 2\nC. Subtema 3\n\n\n El esquema debe ser basado en el siguiente texto: \n\n" . $context;
            case 'fr':
                return "Créez un plan de publication de blog approfondi et complet, avec un maximum de deux niveaux, avec un ton " . $tone . ", en utilisant des chiffres romains pour indiquer les sujets principaux et des lettres de l'alphabet commun pour indiquer les sous-sujets. Le plan doit avoir seulement $maxSubtopics sujets. N'ajoutez pas un troisième niveau de sujets. N'ajoutez pas de sujets internes dans les sous-sujets indiqués par des lettres de l'alphabet commun, par exemple: \n\nOutput correct:\nI. Sujet Principal \n A. Sous-sujet 1 \n B. Sous-sujet 2 \n C. Sous-sujet 3 \n\nOutput incorrect:\nI. Sujet Principal \nA. Sous-sujet 1 \nB. Sous-sujet 2\n   1. Sous-sujet interne 1\n   2. Sous-sujet interne 2\nC. Sous-sujet 3\n\n\n Le plan doit être basé sur le texte suivant: \n\n" . $context;
            case 'de':
                return "Erstellen Sie eine umfassende und umfassende Blog-Post-Gliederung mit maximal zwei Ebenen und einem " . $tone . "-Ton, indem Sie römische Zahlen verwenden, um Hauptthemen und Buchstaben des allgemeinen Alphabets zu verwenden, um Unterthemen anzuzeigen. Die Gliederung darf nur $maxSubtopics Themen haben. Fügen Sie keine dritte Ebene von Themen ein. Fügen Sie keine inneren Themen in die mit Buchstaben des allgemeinen Alphabets angegebenen Unterthemen ein, zum Beispiel: \n\nGutes Ergebnis:\nI. Hauptthema \n A. Unterthema 1 \n B. Unterthema 2 \n C. Unterthema 3 \n\nSchlechtes Ergebnis:\nI. Hauptthema \nA. Unterthema 1 \nB. Unterthema 2\n   1. Inneres Thema 1\n   2. Inneres Thema 2\nC. Unterthema 3\n\n\n Die Gliederung sollte auf dem folgenden Text basieren: \n\n" . $context;
            case 'it':
                return "Crea un piano di pubblicazione del blog approfondito e completo, con un massimo di due livelli, con un tono " . $tone . ", usando numeri romani per indicare gli argomenti principali e lettere dell'alfabeto comune per indicare i sottoargomenti. Il piano deve avere solo $maxSubtopics argomenti. Non aggiungere un terzo livello di argomenti. Non aggiungere argomenti interni nei sottoargomenti indicati con lettere dell'alfabeto comune, ad esempio: \n\nOutput corretto:\nI. Argomento principale \n A. Sotto-argomento 1 \n B. Sotto-argomento 2 \n C. Sotto-argomento 3 \n\nOutput errato:\nI. Argomento principale \nA. Sotto-argomento 1 \nB. Sotto-argomento 2\n   1. Sotto-argomento interno 1\n   2. Sotto-argomento interno 2\nC. Sotto-argomento 3\n\n\n Il piano deve essere basato sul seguente testo: \n\n" . $context;
            case 'ru':
                return "Создайте подробный и полный план публикации блога с максимумом двух уровней, с тональностью " . $tone . ", используя римские цифры для обозначения основных тем и букв общего алфавита для обозначения подтем. План должен иметь только $maxSubtopics тем. Не добавляйте третий уровень тем. Не добавляйте внутренние темы в подтемы, указанные буквами общего алфавита, например: \n\nПравильный вывод:\nI. Основная тема \n A. Подтема 1 \n B. Подтема 2 \n C. Подтема 3 \n\nНеправильный вывод:\nI. Основная тема \nA. Подтема 1 \nB. Подтема 2\n   1. Внутренняя тема 1\n   2. Внутренняя тема 2\nC. Подтема 3\n\n\n План должен быть основан на следующем тексте: \n\n" . $context;
            case 'ja':
                return "最大2レベルで、" . $tone . "のトーンを使用して、主題を示すためにローマ数字を使用し、サブトピックを示すために一般的なアルファベットの文字を使用して、包括的で包括的なブログ投稿のアウトラインを作成します。計画には、$maxSubtopics トピックのみが含まれています。トピックの3番目のレベルを追加しないでください。一般的なアルファベットの文字で指定されたサブトピックに内部トピックを追加しないでください。例：\n\n正しい出力：\nI.メインテーマ \n A.サブトピック1 \n B.サブトピック2 \n C.サブトピック3 \n\n間違った出力：\nI.メインテーマ \nA.サブトピック1 \nB.サブトピック2\n   1.内部トピック1\n   2.内部トピック2\nC.サブトピック3\n\n\n この計画は、次のテキストに基づいています。\n\n" . $context;
            case 'ch':
                return "使用罗马数字表示主题，使用通用字母表示子主题，使用" . $tone . "的语气，创建一个详细而全面的博客发布大纲，最多有两个级别。计划只包含$maxSubtopics 个主题。不要添加第三个级别的主题。不要在用通用字母指定的子主题中添加内部主题。例如：\n\n正确的输出：\nI.主题 \n A.子主题1 \n B.子主题2 \n C.子主题3 \n\n错误的输出：\nI.主题 \nA.子主题1 \nB.子主题2\n   1.内部主题1\n   2.内部主题2\nC.子主题3\n\n\n 该计划应基于以下文本：\n\n" . $context;
            case 'ar':
                return "قم بإنشاء مخطط نشر مدونة مفصل وشامل ، بحد أقصى اثنين من المستويات ، باستخدام الأرقام الرومانية للإشارة إلى الموضوعات الرئيسية وحروف الأبجدية العامة للإشارة إلى الموضوعات الفرعية ، مع " . $tone . " اللهجة. يجب أن يحتوي الخطة على $maxSubtopics موضوعًا فقط. لا تضيف مستوى ثالث من الموضوعات. لا تضيف موضوعات داخلية في الموضوعات الفرعية المشار إليها بحروف الأبجدية العامة ، على سبيل المثال: \n\nالإخراج الصحيح: \nI. الموضوع الرئيسي \n A. الموضوع الفرعي 1 \n B. الموضوع الفرعي 2 \n C. الموضوع الفرعي 3 \n\nالإخراج غير الصحيح: \nI. الموضوع الرئيسي \nA. الموضوع الفرعي 1 \nB. الموضوع الفرعي 2\n   1. الموضوع الداخلي 1\n   2. الموضوع الداخلي 2\nC. الموضوع الفرعي 3\n\n\n يجب أن يكون الخطة مستندة إلى النص التالي: \n\n" . $context;
            case 'ko':
                return "로마 숫자를 사용하여 주제를 나타내고 일반적인 알파벳 문자를 사용하여 하위 주제를 나타내며 " . $tone . " 톤을 사용하여 자세하고 포괄적 인 블로그 게시물 개요를 만듭니다. 최대 2 레벨. 계획에는 $maxSubtopics 개의 주제 만 포함됩니다. 주제의 세 번째 수준을 추가하지 마십시오. 일반적인 알파벳 문자로 지정된 하위 주제에 내부 주제를 추가하지 마십시오. 예 :\n\n올바른 출력 :\nI. 메인 주제 \n A. 하위 주제 1 \n B. 하위 주제 2 \n C. 하위 주제 3 \n\n잘못된 출력 :\nI. 메인 주제 \nA. 하위 주제 1 \nB. 하위 주제 2\n   1. 내부 주제 1\n   2. 내부 주제 2\nC. 하위 주제 3\n\n\n이 계획은 다음 텍스트를 기반으로합니다. \n\n" . $context;
            case 'tr':
                return "Ana konuları göstermek için Roma rakamlarını, alt konuları göstermek için genel harfleri ve " . $tone . " tonunu kullanarak ayrıntılı ve kapsamlı bir blog yayını taslağı oluşturun. En fazla 2 seviye. Plan, yalnızca $maxSubtopics konuyu içermelidir. Konuların üçüncü seviyesini eklemeyin. Genel harflerle belirtilen alt konularda iç konular eklemeyin. Örnek :\n\nDoğru çıktı :\nI. Ana konu \n A. Alt konu 1 \n B. Alt konu 2 \n C. Alt konu 3 \n\nYanlış çıktı :\nI. Ana konu \nA. Alt konu 1 \nB. Alt konu 2\n   1. İç konu 1\n   2. İç konu 2\nC. Alt konu 3\n\n\nBu plan aşağıdaki metne dayanmalıdır. \n\n" . $context;
            case 'pl':
                return "Utwórz szczegółowy i wszechstronny szkic posta na blogu, używając cyfr rzymskich do wskazania tematów głównych, ogólnych liter do wskazania tematów podrzędnych i " . $tone . " tonu. Maksymalnie 2 poziomy. Plan powinien zawierać tylko $maxSubtopics tematów. Nie dodawaj trzeciego poziomu tematów. Nie dodawaj tematów wewnętrznych w tematach podrzędnych wskazanych ogólnymi literami. Przykład :\n\nPoprawny wynik :\nI. Temat główny \n A. Temat podrzędny 1 \n B. Temat podrzędny 2 \n C. Temat podrzędny 3 \n\nNiepoprawny wynik :\nI. Temat główny \nA. Temat podrzędny 1 \nB. Temat podrzędny 2\n   1. Temat wewnętrzny 1\n   2. Temat wewnętrzny 2\nC. Temat podrzędny 3\n\n\nTen plan powinien być oparty na następującym tekście. \n\n" . $context;
            case 'el':
                return "Δημιουργήστε ένα εκτενές σκιαγράφημα άρθρου ιστολογίου, με το πολύ δύο επίπεδα, με τόνο " . $tone . ", χρησιμοποιώντας ρωμαϊκούς αριθμούς για τα κύρια θέματα και γράμματα του αλφαβήτου για τα υπο-θέματα. Το σκιαγράφημα πρέπει να έχει μόνο $maxSubtopics θέματα. Μην κάνετε ενσωμάτωση ενός τρίτου επιπέδου θεμάτων. Μην προσθέτετε εσωτερικά θέματα μέσα στα υπο-θέματα που υποδεικνύονται από τα γράμματα του αλφαβήτου, για παράδειγμα: \n\nΚαλή Έξοδος:\nI. Κύριο Θέμα \n A. Υπο-θέμα 1 \n B. Υπο-θέμα 2 \n C. Υπο-θέμα 3 \n\nΚακή Έξοδος:\nI. Κύριο Θέμα \nA. Υπο-θέμα 1 \nB. Υπο-θέμα 2\n 1. Εσωτερικό θέμα 1\n 2. Εσωτερικό θέμα 2\nC. Υπο-θέμα 3\n\n\n Το σκιαγράφημα πρέπει να βασίζεται στον ακόλουθο κείμενο: \n\n" . $context;
            default:
                return '';
        }
    }


    public function givenFollowingText($text)
    {
        switch ($this->language) {
            case 'en':
                return "Given the following text: \n\n" . $text . "\n\n\n";
            case 'pt':
                return "Tendo como base o seguinte texto: \n\n" . $text . "\n\n\n";
            case 'es':
                return "Teniendo como base el siguiente texto: \n\n" . $text . "\n\n\n";
            case 'fr':
                return "Étant donné le texte suivant: \n\n" . $text . "\n\n\n";
            case 'de':
                return "Angesichts des folgenden Textes: \n\n" . $text . "\n\n\n";
            case 'it':
                return "Dato il seguente testo: \n\n" . $text . "\n\n\n";
            case 'ru':
                return "Учитывая следующий текст: \n\n" . $text . "\n\n\n";
            case 'ja':
                return "次のテキストを考慮してください：\n\n" . $text . "\n\n\n";
            case 'ch':
                return "考虑以下文本：\n\n" . $text . "\n\n\n";
            case 'ar':
                return "بالنظر إلى النص التالي: \n\n" . $text . "\n\n\n";
            case 'ko':
                return "다음 텍스트를 고려하십시오. \n\n" . $text . "\n\n\n";
            case 'tr':
                return "Aşağıdaki metni göz önünde bulundurun: \n\n" . $text . "\n\n\n";
            case 'pl':
                return "Mając na uwadze następujący tekst: \n\n" . $text . "\n\n\n";
            case 'el':
                return "Δεδομένου του ακόλουθου κειμένου: \n\n" . $text . "\n\n\n";
            default:
                return '';
        }
    }

    public function andGivenFollowingContext($text)
    {
        switch ($this->language) {
            case 'en':
                return "And given the following context:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'pt':
                return "E levando em conta o seguinte contexto:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'es':
                return "Y teniendo en cuenta el siguiente contexto:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'fr':
                return "Et compte tenu du contexte suivant:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'de':
                return "Und unter Berücksichtigung des folgenden Kontexts:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'it':
                return "E dato il seguente contesto:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'ru':
                return "И учитывая следующий контекст:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'ja':
                return "次のコンテキストを考慮してください：\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'ch';
                return "并考虑以下上下文：\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'ar':
                return "وبالنظر إلى السياق التالي:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'ko':
                return "다음 컨텍스트를 고려하십시오. \n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'tr':
                return "Ve aşağıdaki bağlamı göz önünde bulundurun:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'pl':
                return "I mając na uwadze następujący kontekst:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            case 'el':
                return "Και λαμβάνοντας υπόψη τον ακόλουθο περιβάλλοντα χώρο:\n\n" . preg_replace('/\s+/', ' ', $text) . "\n\n\n";
            default:
                return '';
        }
    }

    public function expandOn($text, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                return "Using a " . $tone . " tone, and using <h3> tags for subtopics and <p> tags for paragraphs, expand on: \n\n" . $text . "\n\n\nFurther instructions:\nWhen expanding the text, do not create new <h3> inner topics. Instead, increase the number of paragraphs.";
            case 'pt':
                return "Usando um tom " . $tone . ", e usando tags <h3> para os sub-tópicos e tags <p> para os parágrafos, expanda: \n\n" . $text . "\n\n\nOutras instruções:\nAo expandir o texto, não crie novos tópicos internos <h3>. Ao invés disso, aumente o número de parágrafos.";
            case 'es':
                return "Usando un tono " . $tone . ", y usando tags <h3> para los subtemas y tags <p> para los párrafos, expande: \n\n" . $text . "\n\n\nOtras instrucciones:\nAl expandir el texto, no crees nuevos temas internos <h3>. En su lugar, aumenta el número de párrafos.";
            case 'fr':
                return "En utilisant un ton " . $tone . ", et en utilisant des tags <h3> pour les sous-sujets et des tags <p> pour les paragraphes, développez: \n\n" . $text . "\n\n\nAutres instructions:\nLors de l'expansion du texte, ne créez pas de nouveaux sujets internes <h3>. Au lieu de cela, augmentez le nombre de paragraphes.";
            case 'de':
                return "Verwenden Sie einen " . $tone . " Ton und verwenden Sie <h3> Tags für Untertitel und <p> Tags für Absätze, um zu erweitern: \n\n" . $text . "\n\n\nWeitere Anweisungen:\nWenn Sie den Text erweitern, erstellen Sie keine neuen <h3> inneren Themen. Erhöhen Sie stattdessen die Anzahl der Absätze.";
            case 'it':
                return "Usando un tono " . $tone . ", e usando tag <h3> per i sottotitoli e tag <p> per i paragrafi, espandi: \n\n" . $text . "\n\n\nUlteriori istruzioni:\nQuando si espande il testo, non creare nuovi argomenti interni <h3>. Invece, aumentare il numero di paragrafi.";
            case 'ru':
                return "Используя тон " . $tone . ", и используя теги <h3> для подтем и теги <p> для абзацев, расширьте: \n\n" . $text . "\n\n\nДополнительные инструкции:\nПри расширении текста не создавайте новых внутренних тем <h3>. Вместо этого увеличьте количество абзацев.";
            case 'ja':
                return "次のように展開してください：\n\n" . $text . "\n\n\nさらなる指示：\nテキストを展開するときは、新しい<h3>内部トピックを作成しないでください。代わりに、段落の数を増やします。";
            case 'ch':
                return "请扩展以下内容：\n\n" . $text . "\n\n\n进一步的说明：\n在扩展文本时，请勿创建新的<h3>内部主题。相反，增加段落数。";
            case 'ar':
                return "وتوسيع على ما يلي:\n\n" . $text . "\n\n\nمزيد من التعليمات:\nعند توسيع النص ، لا تقم بإنشاء مواضيع داخلية جديدة <h3>. بدلاً من ذلك ، زد عدد الفقرات.";
            case 'ko':
                return "다음을 확장하십시오. \n\n" . $text . "\n\n\n추가 지침 :\n텍스트를 확장할 때 새로운 <h3> 내부 주제를 만들지 마십시오. 대신 단락 수를 늘리십시오.";
            case 'tr':
                return "Aşağıdaki metnin üzerine <h3> etiketleri alt başlıklar ve <p> etiketleri paragraflar için kullanılarak, bir " . $tone . " tonu kullanarak genişletin:\n\n" . $text . "\n\n\nDaha fazla talimat:\nMetni genişletirken, yeni <h3> iç konuları oluşturmayın. Bunun yerine, paragraf sayısını artırın.";
            case 'pl':
                return "Rozwiń następujący tekst:\n\n" . $text . "\n\n\nDodatkowe instrukcje:\nPodczas rozszerzania tekstu nie twórz nowych wewnętrznych tematów <h3>. Zamiast tego zwiększ liczbę akapitów.";
            case 'el':
                return "Χρησιμοποιώντας έναν τόνο " . $tone . ", και χρησιμοποιώντας ετικέτες <h3> για τους υποτίτλους και ετικέτες <p> για τους παραγράφους, επεκτείνετε: \n\n" . $text . "\n\n\nΠεραιτέρω οδηγίες:\nΚατά τη διάρκεια της επέκτασης του κειμένου, μη δημιουργείτε νέα εσωτερικά θέματα <h3>. Αντ 'αυτού, αυξήστε τον αριθμό των παραγράφων.";
            default:
                return '';
        }
    }

    public function writeMetaDescription($text, $tone = 'casual', $keyword = null)
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        switch ($this->language) {
            case 'en':
                $withKeyword = $keyword ? "using the keyword $keyword," : "";
                return "Write a meta description of a maximum of 20 words, with a $tone tone, $withKeyword for the following text: \n\n" . $text;
            case 'pt':
                $withKeyword = $keyword ? "usando a palavra-chave $keyword," : "";
                return "Escreva uma meta description com no máximo 20 palavras, com um tom $tone, $withKeyword para o seguinte texto: \n\n" . $text;
            case 'es':
                $withKeyword = $keyword ? "usando la palabra clave $keyword," : "";
                return "Escribe una meta description con un máximo de 20 palabras, con un tono $tone, $withKeyword para el siguiente texto: \n\n" . $text;
            case 'fr':
                return "Écrivez une méta description d'un maximum de 20 mots, avec un ton $tone, pour le texte suivant: \n\n" . $text;
            case 'de':
                return "Schreiben Sie eine Meta-Beschreibung von maximal 20 Wörtern mit einem $tone Ton für den folgenden Text: \n\n" . $text;
            case 'it':
                return "Scrivi una meta descrizione di un massimo di 20 parole, con un tono $tone, per il seguente testo: \n\n" . $text;
            case 'ru':
                return "Напишите мета-описание максимум из 20 слов, с тоном $tone, для следующего текста: \n\n" . $text;
            case 'ja':
                return "次のテキストについて、最大20語のメタ記述を、$tone トーンで書いてください：\n\n" . $text;
            case 'ch':
                return "请用最多20个单词用$tone 语调写出以下文本的元描述：\n\n" . $text;
            case 'ar':
                return "اكتب وصفًا وصفيًا بحد أقصى 20 كلمة ، بلهجة $tone ، للنص التالي: \n\n" . $text;
            case 'ko':
                return "다음 텍스트에 대해 최대 20 단어의 메타 설명을 $tone 톤으로 작성하십시오. \n\n" . $text;
            case 'tr':
                return "Aşağıdaki metin için, $tone tonunda, en fazla 20 kelime içeren bir meta açıklaması yazın: \n\n" . $text;
            case 'pl':
                return "Napisz metaopis o maksymalnej długości 20 słów, z tonem $tone, dla następującego tekstu: \n\n" . $text;
            case 'el':
                return "Γράψτε μια μετα-περιγραφή μέγιστου 20 λέξεων, με τόνο $tone, για τον ακόλουθο κείμενο: \n\n" . $text;
            default:
                return '';
        }
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }
}
