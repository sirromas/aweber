$(document).ready(function () {

    console.log("ready!");
    var domain = 'www.theadriangee.com';
    //var domain = 'mycodebusters.com';

    $('#users_table').DataTable();
    $('#list_table').DataTable();
    $('#settings_table').DataTable();

    var site_url = 'http://' + domain + '/aw-cpanel/';

    function get_settings_table() {
        var url = '/aw-cpanel/get_settings_table.php';
        $.post(url, {id: 1}).done(function (data) {
            $('#settigs_data').html(data);
            $('#settings_table').DataTable();
        });
    }

    function get_users_table() {
        var url = '/aw-cpanel/get_users_table.php';
        $.post(url, {id: 1}).done(function (data) {
            $('#users_table_wrapper').html(data);
            $('#users_table').DataTable();
        });
    }


    $("body").click(function (event) {

        console.log('Click Event ID: ' + event.target.id);

        if (event.target.id == 'logout') {
            if (confirm('Logout from the system?')) {
                var url = '/aw-cpanel/logout.php';
                $.post(url, {id: 1}).done(function (data) {
                    console.log(data);
                    document.location = site_url;
                }); // end of post
            }
        }


        if (event.target.id == 'add_new_list_settings') {
            var src = $('#list_dropdown_src').val();
            var dst = $('#list_dropdown_dst').val();
            var total = $('#click_num_dropdown').val();
            var type = $('#click_types_dropdown').val();

            if (src == 0 || dst == 0 || total == 0 || type == 0) {
                $('#add_err').html('Please provide all required fields');
            } // end if
            else {
                $('#add_err').html('');
                var item = {src: src, dst: dst, total: total, type: type};
                console.log('Item: ' + JSON.stringify(item));
                if (confirm('Add new settings?')) {
                    $('#ajax_loader').show();
                    var url = '/aw-cpanel/add_config_item.php';
                    $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                        console.log(data);
                        $('#ajax_loader').hide();
                        get_settings_table();
                        //document.location.reload();
                    }); // end of post
                } // end if confirm
            } // end else
        }

        if (event.target.id.indexOf("config_del_") >= 0) {
            var id = event.target.id.replace("config_del_", "");
            if (confirm('Delete current settings?')) {
                var url = '/aw-cpanel/del_config_item.php';
                $.post(url, {id: id}).done(function (data) {
                    console.log(data);
                    get_settings_table();
                });
            }
        }

        if (event.target.id.indexOf("list_edit_") >= 0) {
            var id = event.target.id.replace("list_edit_", "");
            var url = '/aw-cpanel/get_list_edit_dialog.php';
            $.post(url, {id: id}).done(function (data) {
                $("body").append(data);
                $("#myModal").modal('show');
                document.body.style.overflow = 'auto';
            });
        }

        if (event.target.id.indexOf("user_edit_") >= 0) {
            var id = event.target.id.replace("user_edit_", "");
            var url = '/aw-cpanel/get_user_edit_dialog.php';
            $.post(url, {id: id}).done(function (data) {
                $("body").append(data);
                $("#myModal").modal('show');
                document.body.style.overflow = 'auto';
            });
        }

        if (event.target.id == 'cancel_list_edit_dialog') {
            $("[data-dismiss=modal]").trigger({type: "click"});
            $("#myModal").remove();
            $('.modal-backdrop').remove();
        }

        if (event.target.id == 'update_user_data') {
            var userid = $('#userid').val();
            var fname = $('#fname').val();
            var lname = $('#lname').val();
            var username = $('#username').val();
            var password = $('#password').val();

            if (fname == '' || lname == '' || username == '' || password == '') {
                $('#user_err').html('Please provide all required fields');
            } // end if
            else {
                $('#user_err').html('');
                var item = {userid: userid, fname: fname, lname: lname, username: username, password: password};
                var url = '/aw-cpanel/update_user_data.php';
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    console.log(data);
                    $("[data-dismiss=modal]").trigger({type: "click"});
                    $("#myModal").remove();
                    $('.modal-backdrop').remove();
                    get_users_table();
                });
            } // end else
        }


        if (event.target.id == 'update_list_subs') {
            var id = $('#config_id').val();
            var src = $('#list_dropdown_src_edit').val();
            var dst = $('#list_dropdown_dst_edit').val();
            var lst = $('#click_types_edit_dropdown').val();

            if (src == 0 || dst == 0 || lst == 0) {
                $('#subs_err').html('Please provide all required fields');
            } // end if
            else {
                $('#subs_err').html('');
                var item = {id: id, src: src, dst: dst, lst: lst};
                var url = '/aw-cpanel/update_subs_data.php';
                $.post(url, {item: JSON.stringify(item)}).done(function (data) {
                    console.log(data);
                    $("[data-dismiss=modal]").trigger({type: "click"});
                    $("#myModal").remove();
                    $('.modal-backdrop').remove();
                    get_settings_table();
                }); // end of post
            } // end else
        }


    }); // end of $("body").click


}); // end of document ready