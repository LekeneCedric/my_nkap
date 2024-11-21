<?php

namespace App\Operation\Infrastructure\Services;

use App\Operation\Domain\Services\AIService;
use App\Operation\Domain\VO\MakeAIOperationServiceResponseVO;
use App\Shared\Infrastructure\HttpClient\HttpClient;
use Exception;
use Ramsey\Uuid\Uuid;

class GeminiAIService implements AIService
{

    public function makeOperation(array $accounts, array $categories, string $message, string $currentDate, string $language): MakeAIOperationServiceResponseVO
    {
        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = env('GEMINI_API_URL').'?key='.$apiKey;

        // Prepare the content parts
        $contents = [
            [
                "text" => '
                    You are an AI financial assistant. Analyze the user message and return operations in JSON format from these inputs:

                    1. **Categories**: Array of { id, label } (e.g., { 1, "Food" }).
                    2. **Accounts**: Array of { id, label } (e.g., { 1, "Cash" }).
                    3. **Current Date**: "YYYY-MM-DD HH:mm:ss".
                    4. **User Message**: Describe the activity.

                    Your task:
                    - Identify operation type (1=INCOME, 2=EXPENSE), amount, category, and account (first if only one).
                    - Return operations if a categoryId is found in this format:
                    [
                    {
                    "accountId": "string",
                    "type": 1|2,
                    "amount": number,
                    "categoryId": "string",
                    "date": "string",
                    "title": "string"
                    }
                    ]
                    Use UTF-8 encoding for operation strings.
                ',
            ],
            [
                "text" => "List of given categories : (" . implode(", ", array_map(function($ca) {
                        return "{ id: {$ca['id']}, label: '{$ca['label']}' }";
                    }, $categories)) . ")
                    \n List of given accounts : (" . implode(", ", array_map(function($ac) {
                        return "{ id: {$ac['id']}, label: '{$ac['label']}' }";
                    }, $accounts)) . ")",
            ],
            [
                "text" => "Today is : $currentDate",
            ],
            [
                "text" => "specified language : $language"
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
                    'accountId' => $data['accountId'],
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
