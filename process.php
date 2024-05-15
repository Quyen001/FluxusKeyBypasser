<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy URL từ form input
    $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

    // Kiểm tra URL hợp lệ
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        die('Invalid URL');
    }

    // Bao gồm file simple_html_dom.php
    require 'vendor/simple_html_dom.php';

    // Khởi tạo cURL session
    $ch = curl_init();

    // Thiết lập các tùy chọn cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Referer: https://linkvertise.com/",
        "User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, như Gecko) Chrome/124.0.0.0 Mobile Safari/537.36",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "Accept-Encoding: gzip, deflate, br, zstd",
        "Accept-Language: vi,vi-VN;q=0.9,en-US;q=0.8,en;q=0.7"
    ]);

    // Thực hiện yêu cầu
    $response = curl_exec($ch);

    // Kiểm tra lỗi cURL
    if(curl_errno($ch)) {
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    }

    // Đóng cURL session
    curl_close($ch);

    // Giải nén dữ liệu nếu cần thiết
    if (substr($response, 0, 2) == "\x1f\x8b") {
        $response = gzdecode($response);
    } elseif (substr($response, 0, 3) == "\x1f\x8b\x08") {
        $response = gzinflate(substr($response, 10, -8));
    }

    // Kiểm tra xem phản hồi có hợp lệ hay không
    if ($response === false) {
        die('Error fetching the URL');
    }

    // Phân tích cú pháp HTML
    $html = str_get_html($response);

    if (!$html) {
        die('Error parsing HTML');
    }

    // Tìm phần tử <code> và lấy nội dung
    $code_element = $html->find('code', 0);

    if ($code_element) {
        $key = $code_element->plaintext;
        echo 'Code: ' . htmlspecialchars($key);
    } else {
        echo 'Code element not found';
    }
} else {
    echo 'Invalid request method';
}
?>
