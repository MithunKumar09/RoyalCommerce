<?php

if (Session::has('success')) {
    echo '<script>
                toastr.success("'.Session::get('success').'")
            </script>';
}
if (Session::has('unsuccess')) {
    echo '<script>
                toastr.error("'.Session::get('unsuccess').'")
            </script>';
}

