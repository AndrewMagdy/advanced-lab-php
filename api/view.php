<?php

class View {
    public function renderJson($response) {
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($response);
    }
}
