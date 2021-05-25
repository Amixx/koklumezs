var localhosts = ["localhost", "127.0.0.1"];
var origin = window.location.origin;

var IS_LOCAL = false;
localhosts.forEach(function(host){
    if(origin.indexOf(host) !== -1){
        IS_LOCAL = true;
    }
});

var IS_PROD = !IS_LOCAL;

function getUrl(url) {
    var prefix = IS_PROD ? '/sys' : '';
    return prefix + url;
}

$(document).ready(function() {
    if ($('select').length) {
        $('select').select2();
        $('select.tags').select2({
            tags: true,
            insertTag: function(data, tag) {
                // Insert the tag at the end of the results
                data.push(tag);
            }
        });
    }

    $(function() {
        $('img[title]').tooltip()
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register(getUrl('/sw.js'), {
            scope: getUrl('/')
        });
    }


    /* When the user scrolls down, hide the navbar. When the user scrolls up, show the navbar */
    var prevScrollpos = window.pageYOffset;
    var $nav = $("#navbar");
    var $chatBtn = $("#chat-btn-with-icons")

    window.onscroll = function() {
        var currentScrollPos = window.pageYOffset;
        if (prevScrollpos > currentScrollPos) {
            $nav.css("top", 0);
            if($chatBtn) $chatBtn.css("top", "20px");
        } else {
            $nav.css("top", "-90px");
            if($chatBtn) $chatBtn.css("top", "-110px");
        }
        prevScrollpos = currentScrollPos;
    }

    var deferredPrompt;
    var $installButton =$("#install-prompt");
    var $promtModal = $("#a2hs-modal");
    var $promptClose = $("#a2hs-modal-close");
    var $promptInstall = $("#a2hs-modal-install");

    $promptClose.on('click', function(){
        $promtModal.modal('hide');
    });
    $promptInstall.on('click', function(){
        handleDeferredPrompt();
    });

    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;

        if(!$('#myModal').hasClass('in')) $installButton.show();
        $promtModal.modal('show');
    });

    $installButton.on('click', function() {
        $installButton.css("background", "gainsboro");
        handleDeferredPrompt();
    });

    function handleDeferredPrompt(){
        deferredPrompt.prompt();

        deferredPrompt.userChoice.then(function(choiceResult) {
            if (choiceResult.outcome === 'accepted') {
                $installButton.hide();
                $promtModal.modal('hide');
            } else {
                $installButton.css("background", "#007D82");
            }
        });
    }

    $(".ReplyButton").on('click', function(){
        var newText = this.innerText === "Atbildēt" ? "Atcelt" : "Atbildēt";
        this.innerText = newText;
        var commentId = this.id.split("-")[1];
        var responseClass = ".response-" + commentId;
        var responseElement = $(responseClass);
        responseElement.toggleClass("hidden");
    });

    var userNavbarLessonDropdown = document.querySelector(".navbar-lessons-dropdown-toggle a");
    if (userNavbarLessonDropdown){
        userNavbarLessonDropdown.href = "/lekcijas";
        $(".navbar-lessons-dropdown-toggle a").removeAttr("data-toggle");
    }    

    setupArchiveSearchByCategory();
    setupLectureFilterByDifficulty();
    setupAssignUserListFilters();

    $("select[name='SignUpForm[ownsInstrument]']").on('change', function(){  
        if ($("span.select2").hasClass('select-warning')) {
            $("span.select2").removeClass("select-warning");
            let div = document.getElementById('has-instrument');
            div.removeChild(div.lastChild);
        }   
        if(parseInt(this.value) === 1){
            $("div.has-experience").addClass("active");
        }else {
            $("div.has-experience").removeClass("active"); 
        }
    });

    addPopoverToElement(
        ".info-school-email",
        "<p>Skolas e-pasts tiek izmantots ziņojumu nosūtīšanai skolēniem, kā arī uz šo e-pastu tiek sūtīti paziņojumi.</p>"
    );

    $("#sentinvoices-paid_date").attr("autocomplete", "off");

    setupVideoPlayers();
});

function addPopoverToElement($selector, html){
    $($selector).popover({
        html: true,
        placement: 'bottom',
        trigger: 'hover',
        content: function(){
            return html;
        }
    });
}

function setupArchiveSearchByCategory(){
    var selectors = {
        checkboxes: {
            favourites: "#only_favourites",
            stillLearning: "#only_still_learning"
        },
        icons: {
            favourites: ".icon-favourite",
            stillLearning: ".icon-still-learning"
        }
    }

    addCheckboxEventListener(selectors.checkboxes.favourites);
    addCheckboxEventListener(selectors.checkboxes.stillLearning);

    function addCheckboxEventListener(selector){
        $(selector).on("input", function () {
            filterByCategory(selector, this.checked);
        });
    }    

    function filterByCategory(selector, isChecked) {
        if (isChecked) {
            $(getOtherCheckboxSelector(selector)).prop("checked", false);
            $(".lecture-wrap").css("display", "none");
            $(getIconSelector(selector)).each(function () {
                this.parentElement.style.display = "block";
            });
        } else {
            $(".lecture-wrap").css("display", "block");
        }
    }

    function getOtherCheckboxSelector(selector) {
        return selector === selectors.checkboxes.favourites
            ? selectors.checkboxes.stillLearning
            : selectors.checkboxes.favourites;
    }

    function getIconSelector(checkboxSelector) {
        return checkboxSelector === selectors.checkboxes.favourites
            ? selectors.icons.favourites
            : selectors.icons.stillLearning;
    }
}

function setupLectureFilterByDifficulty(){
    $("#assign-page-main .select2.select2-container").on("click", function(){  
        var preferredDifficulty = $("#PreferredLectureDifficulty").val()
        if (!preferredDifficulty){
            return;
        }

        $("li.select2-results__option").each(function(){
            if (!this.innerText) {
                return;
            }

            var parts = this.innerText.split("(");
            var difficulty = parseInt(parts[parts.length-1].replace(")", "").trim());
            
            if(difficulty !== parseInt(preferredDifficulty)){
                $(this).css("display", "none");
                $(document).scroll();
            }
        })
    })
}

function setupAssignUserListFilters(){
    var SUB_TYPES = {
        free: "free",
        paid: "paid",
        lead: "lead",
        pausing: "pausing"
    }

    function getSubTypeSelector(type) {
        return ".subscription-type-selector.type-" + type;
    }    

    var subTypeSelectors = Object.keys(SUB_TYPES).map(function (subType) {
        return getSubTypeSelector(subType);
    });

    function getSelectedSubTypes() {
        var subTypes = [];

        if ($(getSubTypeSelector(SUB_TYPES.free)).prop("checked")) subTypes.push("free");
        if ($(getSubTypeSelector(SUB_TYPES.paid)).prop("checked")) subTypes.push("paid");
        if ($(getSubTypeSelector(SUB_TYPES.lead)).prop("checked")) subTypes.push("lead");
        if ($(getSubTypeSelector(SUB_TYPES.pausing)).prop("checked")) subTypes.push("pausing");

        return subTypes;
    }

    

    function setupAssignFilterByLanguage() {
        $("#UserLanguageSelector").on("change", function () {
            var lang = this.value;
            var subTypes = getSelectedSubTypes();

            modifyViewHrefOnFilter("lang", lang);
            filterAssignStudentList($("#UserLanguageSelector").val(), subTypes);
        });
    }

    function setupAssignFilterBySubscriptionType() {
        subTypeSelectors.forEach(function (selector) {
            $(selector).on("change", function () {
                var subTypes = getSelectedSubTypes();

                modifyViewHrefOnFilter("subTypes", subTypes.join(","));
                filterAssignStudentList($("#UserLanguageSelector").val(), subTypes);
            });
        })

        $(getSubTypeSelector(SUB_TYPES.free)).trigger("click");
        $(getSubTypeSelector(SUB_TYPES.paid)).trigger("click");
        $(getSubTypeSelector(SUB_TYPES.lead)).trigger("click");
    }

    function filterAssignStudentList(lang, subTypes) {
        $("#AssignTable tr").each(function () {
            this.style.display = "";
        });

        var $langElems = $("td.user-language");
        $langElems.each(function () {
            var $subTypeElem = $(this).next()[0];
            var $userStatusElem = $(this).next().next()[0];
            var langText = this.innerText;
            var subTypeText = $subTypeElem.innerText;
            var isUserPassive = parseInt($userStatusElem.innerText.trim()) === 11;

            if (shouldHideRow(lang, langText, subTypes, subTypeText, isUserPassive)) {
                this.parentElement.style.display = "none";
            }
        });

        function shouldHideRow(lang, langText, subTypes, subTypeText, isUserPassive) {
            var hideRow;
            var showAllSubTypes = subTypes.length === 0 || subTypes.length === 4;
            var onlyPausing = subTypes.length === 1 && subTypes[0] === "pausing";

            if (lang === "all") {
                if (showAllSubTypes) {
                    hideRow = false;
                } else if (onlyPausing) {
                    hideRow = !isUserPassive;
                } else if (isUserPassive && subTypes.indexOf("pausing") === -1 ) {
                    return true;
                } else {
                    hideRow = subTypes.indexOf(subTypeText) === -1;
                }
            } else {
                if (showAllSubTypes) {
                    hideRow = lang !== langText;
                } else if (onlyPausing) {
                    hideRow = lang !== langText && !isUserPassive;
                } else {
                    hideRow = lang !== langText || subTypes.indexOf(subTypeText) === -1;
                }
            }           

            return hideRow;
        }
    }

    //TODO: refaktorēt šo
    function modifyViewHrefOnFilter(name, value) {
        $("#AssignTable tbody tr td a[title='Apskatīt']").each(function () {
            var hrefParts = this.href.split(/[?&]+/)
            var newHrefPart = value !== "all" ? name + "=" + value : "";
            if (hrefParts.length === 1) {
                this.href += "?" + newHrefPart;
            } else if (hrefParts.length === 2) {
                if (hrefParts[1].indexOf(name) !== -1) {
                    if (newHrefPart) {
                        this.href = hrefParts[0] + "?" + newHrefPart;
                    } else {
                        this.href = hrefParts[0];
                    }
                } else {
                    this.href = this.href + "&" + newHrefPart;
                }
            } else {
                if (hrefParts[1].indexOf(name) !== -1) {
                    if (newHrefPart) {
                        this.href = hrefParts[0] + "?" + newHrefPart + "&" + hrefParts[2];
                    } else {
                        this.href = hrefParts[0] + "?" + hrefParts[2];
                    }
                } else {
                    if (newHrefPart) {
                        this.href = hrefParts[0] + "?" + newHrefPart + "&" + hrefParts[1];
                    } else {
                        this.href = hrefParts[0] + "?" + hrefParts[1];
                    }

                }
            }
        })
    }

    setupAssignFilterByLanguage();
    setupAssignFilterBySubscriptionType();

    loadUnreadMessagesCount();
}

function reloadChat(message, clearChat, showSpinner) {
    var url = $(".btn-send-comment").data("url");
    var model = $(".btn-send-comment").data("model");
    var recipient_id = $(".btn-send-comment").data("recipient_id");

    if(showSpinner) hideChatContent();

    $.ajax({
        url: url,
        type: "POST",
        data: {message: message, model: model, recipient_id: recipient_id},
        success: function (data) {
            loadUnreadMessagesCount();

            data = JSON.parse(data);
            if (clearChat) $("#chat_message").val("");
            $("#chat-box").html(data.content);
            if(data.userList){
                $("#chat-user-list").html(data.userList);
            }

            showChatContent();
        }
    });
}

function scrollChatToBottom(){
    var useTimeout = false;
    $("#chat-box-container").scrollTop(function() {
        useTimeout = this.scrollHeight === 0;
        return this.scrollHeight;
    });

    //šitā nekad nevajag darīt!!! :)
    if(useTimeout){
        setTimeout(function(){
            $("#chat-box-container").scrollTop(function() {
                return this.scrollHeight;
            });
        }, 200);        
    }
}

var $unreadCount = $(".chat-unread-count");

function loadUnreadMessagesCount() {
    var url = IS_PROD ? "/sys/chat/get-unread-count" : "/chat/get-unread-count";

    $unreadCount.hide();

    $.ajax({
        url: url,
        type: "POST",
        success: function (count) {
            if(parseInt(count) > 0){
                $unreadCount.html(count);
                $unreadCount.show();
            }else{
                $unreadCount.hide();
            }
        }
    });
}

function isChatOpen(){
    return $("#chatModal:visible").length > 0;
}


setInterval(function () {
    if(isChatOpen()) reloadChat('', false, false);
}, 600000); // katru minūti

setInterval(function () {
    if(!isChatOpen()) loadUnreadMessagesCount();
}, 120000); // katru otro minūti

$(".btn-send-comment").on("click", function () {
    var message = $("#chat_message").val();
    $(".chat-unread-count").hide();
    reloadChat(message, true, false);
});

$("#chat-toggle-button").on('click', function(){
    reloadChat("", true, true);
});

$(document).on('click', ".chat-user-item", function(){
    var newRecipientId = $(this).data("userid");
    $(".btn-send-comment").data("recipient_id", newRecipientId);

    reloadChat("", true, true);
})

$(document).on('click', ".chat-with-student", function(){
    var newRecipientId = $(this).data("userid");
    $(".btn-send-comment").data("recipient_id", newRecipientId);

    reloadChat("", true, true);
    $("#chatModal").modal('show');
})

var $chatSpinner = $("#chat-spinner");
var $chatContent = $("#chat-content-container");
function hideChatContent(){
    $chatContent.hide();
    $chatSpinner.show();
}
function showChatContent(){
    $chatContent.show();
    $chatSpinner.hide();
    scrollChatToBottom();
}

$("#export-sent-invoices").on("click", exportSentInvoices);

function exportSentInvoices(){
    var csvContent = "\uFEFF";

    var $rows = $("#sent-invoices-table tr:visible:not(:first-child)");
    $rows.each(function(i, row){
        var r = [];
        var $cols = $(row).find("td:not(:first-child)");
        $cols.each(function(i, col){
            r.push(col.innerText);
        });
        csvContent += r.join(",") + "\r\n";
    });

    exportToCSV(csvContent, 'dowload.csv', 'text/csv;encoding:utf-8,%EF%BB%BF');
}

var exportToCSV = function(content, fileName, mimeType) {
  var a = document.createElement('a');
  mimeType = mimeType || 'application/octet-stream';

  if (navigator.msSaveBlob) { // IE10
    navigator.msSaveBlob(new Blob([content], {
      type: mimeType
    }), fileName);
  } else if (URL && 'download' in a) { //html5 A[download]
    a.href = URL.createObjectURL(new Blob([content], {
      type: mimeType
    }));
    a.setAttribute('download', fileName);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  } else {
    location.href = 'data:application/octet-stream,' + encodeURIComponent(content); // only this mime type is supported
  }
}

var $sentInvoicesRows = $("#sent-invoices-table tbody tr");
$("input[name='sent-invoices-filter']").on("input", fiterSentInvoices);
$('#invoices-month-selector').on("select2:select", fiterSentInvoices);
$('#invoices-year-selector').on("select2:select", fiterSentInvoices);

function fiterSentInvoices(){
    var searchValue = $("input[name='sent-invoices-filter']").val().toLowerCase();
    var year = $('#invoices-year-selector').select2().val();
    var month = $('#invoices-month-selector').select2().val();
    
    var filterByText = searchValue.length >= 4;
    var filterByDate = year !== "" && month !== "";

    if(filterByDate){
        year = parseInt(year);
        month = parseInt(month);
        
        var firstDay = new Date(year, month, 1);
        var lastDay = new Date(year, month + 1, 0);
       
        var firstDayDate = makeDateString(firstDay);
        var lastDayDate = makeDateString(lastDay);
    }

    
    if(filterByText || filterByDate){
        $sentInvoicesRows.each(function(i, row){
            var $row = $(row);
            var $rowText = $row.text().toLowerCase();
            var $rowDate = $row.find("td:nth-child(2)").text();

            if(filterByText && $rowText.indexOf(searchValue) === -1) $row.hide();
            else if(filterByDate && $rowDate < firstDayDate || $rowDate > lastDayDate){
                $row.hide()
            } else $row.show();
        });
    } else {
        $sentInvoicesRows.show();
    }
}

function makeDateString(date){
    return date.getFullYear()
        + '-' + leadingZero(date.getMonth()+1)
        + '-' + leadingZero(date.getDate())
}

function leadingZero(string){
    return ('0' + String(string)).slice(-2);
}

$("[data-role=evaluation-emoji]").on("click", submitLessonEvaluation);

function submitLessonEvaluation(){
    var $hiddenInput = $(this.parentElement).siblings("[name=difficulty-evaluation]");
    $hiddenInput.val(this.dataset.value);
    $(this).closest("form").submit();
}

$("[id^=lesson_modal]").on("hidden.bs.modal", pausePlayerOnModalClose);

function pausePlayerOnModalClose(){
    var $pauseBtn = $(this).find(".plyr__control.plyr__control--overlaid.plyr__control--pressed");
    if($pauseBtn) $pauseBtn.trigger('click');
}


function setupVideoPlayers(){
    var players = {};

    var options = {
        controls: [
            'play-large', // The large play button in the center
            'restart', // Restart playback
            'rewind', // Rewind by the seek time (default 10 seconds)
            'play', // Play/pause playback
            'fast-forward', // Fast forward by the seek time (default 10 seconds)
            'progress', // The progress bar and scrubber for playback and buffering
            'current-time', // The current time of playback
            'duration', // The full duration of the media
            'mute', // Toggle mute
            'volume', // Volume control
            //'captions', // Toggle captions
            'settings', // Settings menu
            'pip', // Picture-in-picture (currently Safari only)
            'airplay', // Airplay (currently Safari only)
            //'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
            'fullscreen', // Toggle fullscreen
        ],
        autopause: true,
    }


    Array.from(document.querySelectorAll("[data-role='player']")).forEach(function(video) {
        players[video.id] = new Plyr(video, options);
        players[video.id].poster = posters[video.id];
    });
}