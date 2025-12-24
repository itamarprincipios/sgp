<?php

class Controller {
    protected function view($view, $data = []) {
        return view($view, $data);
    }
}
