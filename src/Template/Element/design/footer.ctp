<?php
/**
 * @var \App\View\AppView $this
 */

if (isset($this->Js)) {
    echo $this->Js->writeBuffer(['inline' => false, 'block' => true]);
}
