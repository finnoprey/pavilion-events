<?php
    /**
     * Software Development SAT - Pavilion Events Management System (PEMS)
     *
     * To be included within other pages, the modal renderer finds and
     * renders messages set in the users session to the screen in popup.
     *
     * @author Finn Scicluna-O'Prey <finn@oprey.co>
     *
     */

    include_once('./utils/helpers_validation.php');

    // Check if any of the possible messages are set
    if (exists('error_message', $_SESSION)) {
        $message = $_SESSION['error_message'];
        // convert the new line characters to <br> elements
        $converted_message = nl2br($message);
        echo <<<HTML
            <dialog open class="modal error">
                <h1>Error!</h1>
                <p>$converted_message</p>
                <form method="dialog">
                    <button>OK</button>
                </form>
            </dialog>
        HTML;

        unset($_SESSION['error_message']);
    }
    if (exists('warning_message', $_SESSION)) {
        $message = $_SESSION['warning_message'];
        // convert the new line characters to <br> elements
        $converted_message = nl2br($message);
        echo <<<HTML
            <dialog open class="modal warning">
                <h1>Warning</h1>
                <p>$converted_message</p>
                <form method="dialog">
                    <button>Dismiss</button>
                </form>
            </dialog>
        HTML;

        unset($_SESSION['warning_message']);
    }
    if (exists('success_message', $_SESSION)) {
        $message = $_SESSION['success_message'];
        // convert the new line characters to <br> elements
        $converted_message = nl2br($message);
        echo <<<HTML
            <dialog open class="modal success">
                <h1>Success!</h1>
                <p>$converted_message</p>
                <form method="dialog">
                    <button>Dismiss</button>
                </form>
            </dialog>
        HTML;

        unset($_SESSION['success_message']);
    }
?>