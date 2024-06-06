<?php
function generateNewToken($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function updateToken($db, $username) {
    $newToken = generateNewToken();
    $stmt = $db->prepare("UPDATE users SET token = ? WHERE username = ?");
    $stmt->bind_param("ss", $newToken, $username);
    if ($stmt->execute()) {
        return $newToken;
    } else {
        return false;
    }
}

function fetchUserToken($db, $username) {
    $sql = "SELECT token FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['token'];
    } else {
        return null;
    }
}
?>
