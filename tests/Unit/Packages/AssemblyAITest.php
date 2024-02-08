<?php

use App\Packages\AssemblyAI\AssemblyAI;
use App\Packages\AssemblyAI\Exceptions\GetTranscriptionSubtitlesRequestException;
use App\Packages\AssemblyAI\Exceptions\TranscribeRequestException;
use Illuminate\Support\Facades\Http;

afterEach(function () {
});

describe(
    'AssemblyAI package',
    function () {
        it('transcribes an audio file successfully', function () {
            Http::fake([
                'api.assemblyai.com/v2/transcript*' => Http::response([
                    'id' => 'transcription_id',
                    'status' => 'completed',
                    'text' => 'Transcribed text here',
                ], 200),
            ]);

            $assemblyAI = new AssemblyAI();
            $response = $assemblyAI->transcribe('http://example.com/audio.mp3');

            $this->assertIsArray($response);
            $this->assertEquals('Transcribed text here', $response['text']);
        });

        it('throws an exception if transcription failed', function () {
            Http::fake([
                'api.assemblyai.com/v2/transcript*' => Http::response(null, 500),
            ]);

            $this->expectException(TranscribeRequestException::class);

            $assemblyAI = new AssemblyAI();
            $assemblyAI->transcribe('http://example.com/audio.mp3');
        });

        it('retrieves a transcription successfully', function () {
            // Mock the successful response for a transcription retrieval
            Http::fake([
                'api.assemblyai.com/v2/transcript/transcription_id' => Http::response([
                    'id' => 'transcription_id',
                    'status' => 'completed',
                    'text' => 'Transcribed text here',
                ], 200),
            ]);

            $assemblyAI = new AssemblyAI();
            $response = $assemblyAI->getTranscription('transcription_id');

            $this->assertIsArray($response);
            $this->assertEquals('transcription_id', $response['id']);
            $this->assertEquals('completed', $response['status']);
            $this->assertEquals('Transcribed text here', $response['text']);
        });

        it('throws an exception when retrieving transcription subtitles fails', function () {
            // Mock a failed response for subtitle retrieval
            Http::fake([
                'api.assemblyai.com/v2/transcript/transcription_id/vtt' => Http::response(null, 500),
                'api.assemblyai.com/v2/transcript/transcription_id/srt' => Http::response(null, 500),
            ]);

            $this->expectException(GetTranscriptionSubtitlesRequestException::class);

            $assemblyAI = new AssemblyAI();
            $assemblyAI->getTranscriptionSubtitles('transcription_id');
        });

        it('retrieves transcription subtitles successfully', function () {
            Http::fake([
                'api.assemblyai.com/v2/transcript/transcription_id/vtt' => Http::response('VTT content here', 200),
                'api.assemblyai.com/v2/transcript/transcription_id/srt' => Http::response('SRT content here', 200),
            ]);

            $assemblyAI = new AssemblyAI();
            $response = $assemblyAI->getTranscriptionSubtitles('transcription_id');

            $this->assertIsArray($response);
            $this->assertArrayHasKey('vtt', $response);
            $this->assertArrayHasKey('srt', $response);
            $this->assertEquals('VTT content here', $response['vtt']);
            $this->assertEquals('SRT content here', $response['srt']);
        });
    }
)->group('packages');
