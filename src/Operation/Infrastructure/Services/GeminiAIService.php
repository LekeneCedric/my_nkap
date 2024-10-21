<?php

namespace App\Operation\Infrastructure\Services;

use App\Operation\Domain\Services\AIService;
use App\Operation\Domain\VO\MakeAIOperationServiceResponseVO;
use App\Shared\Infrastructure\HttpClient\HttpClient;
use Exception;
use Ramsey\Uuid\Uuid;

class GeminiAIService implements AIService
{

    public function makeOperation(array $categories, string $message, string $currentDate, string $language): MakeAIOperationServiceResponseVO
    {
        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = env('GEMINI_API_URL').'?key='.$apiKey;

        // Prepare the content parts
        $contents = [
            [
                "text" => '
                You are an AI assistant for financial management. Please analyze the user message and return structured operations in JSON format based on the following inputs:
                1. **Categories**: An array of user action categories in the form { id, label } (e.g., { 1, "Food" }, { 2, "Transport" }).
                2. **Current Date**: Format "YYYY-MM-DD HH:mm:ss".
                3. **User Message**: Describe the financial activity.

                Your task:
                - Identify the operation type (INCOME=1, EXPENSE=2), amount, relevant category, and return operations in the format making string utf-8 encoded only if corresponding category found:
                [
                    {
                        "type": TYPE,
                        "amount": number,
                        "categoryId": string,
                        "date": string,
                        "title": string
                    }
                ]
                ',
            ],
            [
                "text" => "List of given categories : (" . implode(", ", array_map(function($ca) {
                        return "{ id: {$ca['id']}, label: '{$ca['label']}' }";
                    }, $categories)) . ")",
            ],
            [
                "text" => "Today is : $currentDate",
            ],
            [
                "text" => $message,
            ],
        ];

        // Prepare the request body
        $requestBody = [
            "contents" => [
                "parts" => $contents
            ],
            "generationConfig" => [
                "temperature" => 0.0,
                "response_mime_type" => "application/json",
            ],
        ];

        // Prepare the headers
        $headers = [
            'Content-Type: application/json',
        ];
        // Make the HTTP call
        $result = HttpClient::httpCall($apiUrl, 'POST', $headers, $requestBody);
        if (!$result) {
            return new MakeAIOperationServiceResponseVO(
                operations: [],
                operationIsOk: false,
                consumedToken: 0,
            );
        }
        $apiTextResponse = $result['candidates'][0]['content']['parts'][0]['text'];
        // Clean the response by trimming whitespace
        $cleanedResponse = trim($apiTextResponse);
        // Parse the cleaned response using the parseJsonWithRetry function
        try {
            $parsedData = json_decode($cleanedResponse, true);
            $parsedData = array_map(function(array $data){
                return [
                    'type' => $data['type'],
                    'amount' => $data['amount'],
                    'categoryId' => $data['categoryId'],
                    'date' => $data['date'],
                    'title' => $data['title'],
                    'uuid' => Uuid::uuid4()->toString(),
                ];
            }, $parsedData);
            return new MakeAIOperationServiceResponseVO(
                operations: $parsedData,
                operationIsOk: true,
                consumedToken: (int)$result['usageMetadata']['totalTokenCount'] ?? 0,
            );
        } catch (Exception) {
            return new MakeAIOperationServiceResponseVO(
                operations: [],
                operationIsOk: false,
                consumedToken: 0,
            );
        }
    }
}
