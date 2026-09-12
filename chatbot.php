<?php
session_start();
header('Content-Type: application/json');
include 'config/db_connection.php';
include 'config/gemini_config.php';

$input = json_decode(file_get_contents('php://input'), true);
$user_question = isset($input['question']) ? trim($input['question']) : '';

if(empty($user_question)){
    echo json_encode(['reply' => 'Please type a question.']);
    exit();
}

// ---- Step 1: Pull ALL available properties as context ----
// (Agar aapke paas bohot zyada properties hon future mein, to yahan
// keyword-based WHERE filter laga sakte hain taake sirf relevant rows aayen)
$properties_context = "";
$result = mysqli_query($conn, "SELECT title, location, price, marla, rooms, bathrooms, status, description FROM properties");

if($result && mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $properties_context .= "- {$row['title']} | Location: {$row['location']} | Rent: PKR " . number_format($row['price']) . "/month | Marla: {$row['marla']} | Rooms: {$row['rooms']} | Bathrooms: {$row['bathrooms']} | Status: {$row['status']} | Description: {$row['description']}\n";
    }
} else {
    $properties_context = "No properties currently listed.";
}

// ---- Step 2: Build the system prompt (AI ko project ka pura context) ----
$system_prompt = "You are the official assistant chatbot for 'Hira Property', a rental property management website based in Gujrat, Pakistan.

Here is how the website works:
- Visitors can browse properties as a guest without logging in.
- To book a property or schedule a visit, a user must Register and Login as a 'Tenant' or 'Owner'.
- After registering, users verify their account via an OTP code sent to their email.
- Tenants can send a rental request ('Book Now') on a property's detail page.
- A Property Manager reviews and approves/rejects rental requests.
- Once approved, a lease contract is created which the tenant must sign, then pay rent via the Payments page.
- Tenants can leave a star rating (1-5) and a written review on any property's detail page.
- There are four roles: Tenant, Owner, Property Manager, and Admin.

Here is the current list of available properties in the database:
$properties_context

Answer the user's question in a short, friendly, helpful way based on the above information. If the question is about a specific property (price, location, rooms, etc.), use the property data above. If you don't have enough information to answer, politely say so and suggest they contact the Hira Property team via the Contact Us page. Keep answers concise (2-4 sentences).

IMPORTANT: Always reply in plain English only, regardless of what language the user's question is written in (even if they ask in Urdu, Roman Urdu, or any other language). Do not use Urdu script under any circumstances.";

// ---- Step 3: Call Gemini API ----
$url = "https://generativelanguage.googleapis.com/v1beta/models/" . GEMINI_MODEL . ":generateContent?key=" . GEMINI_API_KEY;

$payload = [
    "system_instruction" => [
        "parts" => [ ["text" => $system_prompt] ]
    ],
    "contents" => [
        [
            "role" => "user",
            "parts" => [ ["text" => $user_question] ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if($curl_error){
    error_log("Gemini cURL error: " . $curl_error);
    echo json_encode(['reply' => 'Sorry, I could not connect right now. Please try again later.']);
    exit();
}

$data = json_decode($response, true);

if($http_code == 200 && isset($data['candidates'][0]['content']['parts'][0]['text'])){
    $ai_reply = $data['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['reply' => $ai_reply]);
} else {
    error_log("Gemini API error: " . $response);
    echo json_encode(['reply' => 'Sorry, something went wrong. Please try again in a moment.']);
}
?>
