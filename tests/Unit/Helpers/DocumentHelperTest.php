<?php

use App\Helpers\DocumentHelper;

describe('Document Helper', function () {
    it('parses an outline to a raw structure', function () {
        $outline = "1. Main Topic \n A. Subtopic 1 \n B. Subtopic 2 \n C. Subtopic 3 \n
        2. Second Topic \n A. Subtopic \n B. Another subtopic";
        $result = DocumentHelper::parseOutlineToRawStructure($outline);
        expect($result)->toBe([
            [
                'subheader' => 'Main Topic',
                'content' => 'Subtopic 1. Subtopic 2. Subtopic 3.'
            ],
            [
                'subheader' => 'Second Topic',
                'content' => 'Subtopic. Another subtopic.'
            ]
        ]);
    });

    it('parses html tags to a raw structure', function () {
        $html = "<h2>Subtopic 1</h2><p>Paragraph here</p><h2>Subtopic 2</h2><p>Another paragraph here</p>";
        $result = DocumentHelper::parseHtmlTagsToRawStructure($html);
        expect($result)->toBe([
            [
                'subheader' => 'Subtopic 1',
                'content' => '<p>Paragraph here</p>'
            ],
            [
                'subheader' => 'Subtopic 2',
                'content' => '<p>Another paragraph here</p>'
            ]
        ]);
    });

    it('breaks text into blocks of sentences', function () {
        $text = "Some text here. It contains some sentences. Here is one. And here is another one.More one sentence.";
        $result = DocumentHelper::breakTextIntoSentences($text);
        expect($result->toArray())->toBe([
            [
                'sentence_order' => 1,
                'text' => 'Some text here.'
            ],
            [
                'sentence_order' => 2,
                'text' => 'It contains some sentences.'
            ],
            [
                'sentence_order' => 3,
                'text' => 'Here is one.'
            ],
            [
                'sentence_order' => 4,
                'text' => 'And here is another one.'
            ],
            [
                'sentence_order' => 5,
                'text' => 'More one sentence.'
            ]
        ]);
    });

    it('splits text into a sentences array', function () {
        $text = "Some text here. It contains some sentences. Here is one. And here is another one.More one sentence.";
        $result = DocumentHelper::splitIntoSentences($text);
        expect($result)->toBe([
            'Some text here',
            '.',
            ' It contains some sentences',
            '.',
            ' Here is one',
            '.',
            ' And here is another one',
            '.',
            'More one sentence',
            '.'
        ]);
    });

    it('splits sentences into array', function () {
        $sentencesArray = [
            'Some text here',
            '.',
            ' It contains some sentences',
            '.',
            ' Here is one',
            '.',
            ' And here is another one',
            '.',
            'More one sentence',
            '.'
        ];
        $result = DocumentHelper::splitSentencesIntoArray($sentencesArray);
        expect($result)->toBe([
            [
                'Some text here',
                '.'
            ],
            [
                ' It contains some sentences',
                '.'
            ],
            [
                ' Here is one',
                '.'
            ],
            [
                ' And here is another one',
                '.'
            ],
            [
                'More one sentence',
                '.'
            ]
        ]);
    });

    it('parses html to array', function () {
        $html = "<h2>Subtopic 1</h2><p>Paragraph here</p><h2>Subtopic 2</h2><p>Another paragraph here</p>";
        $result = DocumentHelper::parseHtmlToArray($html);
        expect($result)->toBe([
            [
                "tag" => "h2",
                "content" => "Subtopic 1"
            ],
            [
                "tag" => "p",
                "content" => "Paragraph here"
            ],
            [
                "tag" => "h2",
                "content" => "Subtopic 2"
            ],
            [
                "tag" => "p",
                "content" => "Another paragraph here"
            ]
        ]);
    });
})->group('helpers');
