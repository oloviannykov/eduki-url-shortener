import './bootstrap';
import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;
$(document).ready(function () {

    let showListPreloader = function() {
        $('#url-shortener-list').html(
            '<div class="list-preloader"'
                + '<span class="glyphicon glyphicon-loading"></span>'
            + '</div>'
        );
    }
    
    let getListItemHtml = function(itemData) {
        return '<div class="urls-list-item">'
            + `<p><b>${itemData.original_url}</b></p>`
            + '<p>Short URL: '
                + `<a href="${itemData.short_url}" target="_blank">${itemData.short_url}</a>`
            + '</p>'
            + '<p>'
                + `Created ${itemData.created_at}.`
                + ` Used ${itemData.usage_counter} times.`
                + ` Last use ${itemData.last_usage_date}`
            + '</p>'
        + '</div>';
    }

    let getErrorWarning = function(errorData) {
        return '<div class="alert alert-danger">'
            + (errorData.code ? `[${errorData.code}] ` : '')
            + `${errorData.message}`
            + '</div>';
    }

    let refreshItemsList = function() {
        console.log('sending GET to /api/urls');
        showListPreloader();
        $.ajax({
            type: 'GET',
            url: '/api/urls',
            data: '',
            contentType: "application/json",
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                let listHtml = '';
                if (response.error) {
                    listHtml += getErrorWarning(response.error);
                } else {
                    response.items.forEach((item, index) => {
                        listHtml += getListItemHtml(item);
                    });
                }
                $('#url-shortener-list').html(listHtml);
            },
            error: function(error) {
                console.error('refreshItemsList() error:', error);
                $('#url-shortener-list').html(
                    getErrorWarning({
                        message: JSON.stringify(error)
                    })
                );
            }
        });
    }
    refreshItemsList();

    $('#url-shortener-form-button').on("click", function(event) {
        let formData = {
            url: $('#url-shortener-form-url').val(),
        };
        console.log('sending POST to /api/urls/new', formData);
        $.ajax({
            type: 'POST',
            url: '/api/urls/new',
            data: JSON.stringify(formData),
            contentType: "application/json",
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                refreshItemsList();
            },
            error: function(error) {
                console.error('form error:', error);
                $('#url-shortener-form-warning').html(
                    getErrorWarning({
                        code: 'exception',
                        message: JSON.stringify(error)
                    })
                );
            }
        });
    });

});
