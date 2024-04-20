import './bootstrap';
import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;
$(document).ready(function () {

    let showListPreloader = function() {
        $('#url-shortener-list').html(
            '<div class="list-preloader">LOADING</div>'
        );
    }
    
    let getListItemHtml = function(itemData) {
        return '<div class="urls-list-item">'
            + `<p class="list-item-url">${itemData.original_url}</p>`
            + '<p>Short URL: '
                + `<a href="${itemData.short_url}" target="_blank">${itemData.short_url}</a>`
            + '</p>'
            + '<p>'
                + `Created ${itemData.created_at}.`
                + ` <b>Used ${itemData.usage_counter} times.</b>`
                + ` Last use ${itemData.last_usage_date}`
            + '</p>'
        + '</div>';
    }

    let getErrorWarning = function(errorData) {
        return '<div class="alert alert-danger">'
            //+ (errorData.code ? `[${errorData.code}] ` : '')
            + `${errorData.message}`
            + '</div>';
    }

    let getResultMessage = function(response) {
        return '<div class="alert alert-warning">'
            + 'Last result: '
            + `<a href="${response.url}" target="_blank">${response.url}</a>`
            + '</div>';
    }

    let refreshItemsList = function() {
        let endpoint = '/api/urls';
        console.log('sending GET to', endpoint);
        showListPreloader();
        
        fetch(endpoint, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        })
        .then((response) => {
            response.json().then((r) => {
                console.log('Response:', r);
                let listHtml = '';
                if (r.error) {
                    listHtml += getErrorWarning(r.error);
                } else {
                    r.items.forEach((item, index) => {
                        listHtml += getListItemHtml(item);
                    });
                }
                $('#url-shortener-list').html(listHtml);
            });
        })
        .catch((error) => {
            console.error('refreshItemsList() error:', error);
            $('#url-shortener-list').html(
                getErrorWarning({message: error})
            );
        });
        /* alternative implementation using jQuery:
        $.ajax({
            type: 'GET',
            url: endpoint,
            timeout: 10000, //give 10 seconds for response
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
            error: function(xhr, ajaxOptions, error) {
                console.error('refreshItemsList() error:', error);
                $('#url-shortener-list').html(
                    getErrorWarning({message: error})
                );
            }
        });
        */
    }
    refreshItemsList();

    $('#url-shortener-form-button').on("click", function(event) {
        let formData = {
            url: $('#url-shortener-form-url').val(),
        };
        let endpoint = '/api/urls/new';
        console.log('sending POST to', endpoint, formData);

        fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        })
        .then((response) => {
            response.json().then((r) => {
                let msg = '';
                if (r.error) {
                    console.error('form error:', r.error);
                    msg = getErrorWarning(r.error);
                } else {
                    $('#url-shortener-form-url').val(''),
                    msg = getResultMessage(r);
                    refreshItemsList();
                }
                $('#url-shortener-form-result').html(msg);
            });
        })
        .catch((error) => {
            console.error('form error:', error);
            $('#url-shortener-form-result').html(
                getErrorWarning({message: error})
            );
        });
        /* alternative implementation using jQuery:
        $.ajax({
            type: 'POST',
            url: endpoint,
            data: JSON.stringify(formData),
            contentType: "application/json",
            dataType: 'json',
            success: function(response) {
                let resultMessage = '';
                if (response.error) {
                    console.error('form error:', response.error);
                    resultMessage = getErrorWarning(response.error);
                } else {
                    $('#url-shortener-form-url').val(''),
                    resultMessage = getResultMessage(response);
                    refreshItemsList();
                }
                $('#url-shortener-form-result').html(resultMessage);
            },
            error: function(xhr, ajaxOptions, error) {
                console.error('form error:', error);
                $('#url-shortener-form-result').html(
                    getErrorWarning({message: error})
                );
            }
        });
        */
    });

    //todo: to list items add button "Remove" with request to API /api/urls/xxxxx/remove

});
