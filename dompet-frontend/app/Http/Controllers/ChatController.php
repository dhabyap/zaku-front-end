use Illuminate\Support\Facades\Log;

class AIChatAPI
{
    public function __construct()
    {
        $this->endpoint = config('services.ai_chat.endpoint');
        $this->key = config('services.ai_chat.key');
    }

    public function parse($text)
    {
        $response = $this->request('POST', $this->endpoint, [
            'text' => $text,
        ]);

        return $response;
    }

    public function request($method, $endpoint, $data = [])
    {
        $url = $this->endpoint . $endpoint;

        $response = $this->sendRequest($method, $url, $data);

        return $response;
    }

    public function sendRequest($method, $url, $data = [])
    {
        $client = new \GuzzleHttp\Client;

        $response = $client->request($method, $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $data,
        ]);

        return $response;
    }
}

class AIChatAPIResponse
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getResponse()
    {
        return $this->data;
    }
}

class AIChatAPIResponseParser
{
    public function parse($data)
    {
        return $data;
    }
}

class AIChatAPIResponseFormatter
{
    public function format($data)
    {
        return $data;
    }
}