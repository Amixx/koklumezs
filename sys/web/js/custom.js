var IS_PROD = window.location.origin.indexOf("localhost") === -1;

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

    // makeNavbarMultilineForStudents();

    setupLectureFilterByDifficulty();

    setupAssignUserListFilters();

    $("select[name='has-own-instrument']").on('change', function(){  
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

    $('#registration-button').on('click', function(event){
        let select = $("select[name='has-own-instrument']").val();

        if ((select === '') && (!$("span.select2").hasClass('select-warning-border'))){
            event.preventDefault();
            $("span.select2").addClass("select-warning-border");
            let errorMessage = document.createElement("p");
            let text = document.createTextNode("Lūdzu, izvēlieties vienu no variantiem");
            errorMessage.classList.add('select-warning-message');
            errorMessage.appendChild(text);
            document.getElementById('has-instrument').appendChild(errorMessage);
        }

        if ((select !== '') && ($("span.select2").hasClass('select-warning-border'))) {
            $("span.select2").removeClass("select-warning-border");
            let checkbox = document.getElementById('has-instrument');
            checkbox.removeChild(checkbox.lastChild);
        }
        
    })
    
    addPopoverToElement(
        ".info-school-email",
        "<p>Skolas e-pasts tiek izmantots ziņojumu nosūtīšanai skolēniem, kā arī uz šo e-pastu tiek sūtīti paziņojumi.</p>"
    );
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

// function makeNavbarMultilineForStudents(){
//     var navbarItemsSelector = ".navbar-nav.for-students li a";

//     $(navbarItemsSelector).each(function (_, item) {
//         makeItemMultiline(item);       
//     });

//     function makeItemMultiline(item){
//         item.innerHTML = textToMultiline(item.innerText.split("/"));;
//         item.style.lineHeight = "10px";
//     }

//     function textToMultiline(parts){
//         var newText = "";

//         parts.forEach(function(part){
//             newText += "<p>" + part + "</p>";
//         });

//         return newText;
//     }
// }

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
        return selector === selectors.checkboxes.favourites ? selectors.checkboxes.stillLearning : selectors.checkboxes.favourites;
    }

    function getIconSelector(checkboxSelector) {
        return checkboxSelector === selectors.checkboxes.favourites ? selectors.icons.favourites : selectors.icons.stillLearning;
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
            data = JSON.parse(data);
            if (clearChat) $("#chat_message").val("");
            $("#chat-box").html(data.content);
            if(data.userList){
                $("#chat-user-list").html(data.userList);
            }

            if(showSpinner) showChatContent();
        }
    });
}

var $unreadCount = $(".chat-unread-count");

function loadUnreadMessagesCount() {
    var url = IS_PROD ? "/sys/chat/get-unread-count" : "/chat/get-unread-count";

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
}, 5000); // katru piekto sekundi

setInterval(function () {
    if(!isChatOpen()) loadUnreadMessagesCount();
}, 60000); // katru minūti

$(".btn-send-comment").on("click", function () {
    var message = $("#chat_message").val();
    $(".chat-unread-count").hide();
    reloadChat(message, true, false);
});


$("#chat-toggle-button").on('click', function(){
    openChat();
});

function openChat(){
    $unreadCount.hide();

    var url = IS_PROD ? "/sys/user/open-chat" : "/user/open-chat";
    $.ajax({
        url: url,
        type: "POST",
    });
}

$(document).on('click', ".chat-user-item", function(){
    var newRecipientId = $(this).data("userid");
    $(".btn-send-comment").data("recipient_id", newRecipientId);

    reloadChat("", true, true);
})

$(document).on('click', ".chat-with-student", function(){
    var newRecipientId = $(this).data("userid");
    $(".btn-send-comment").data("recipient_id", newRecipientId);

    reloadChat("", true, true);
    openChat();
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
}

$('.rent-or-buy-radio input[type="radio"]').click(function(){
    var paymentType = $(this).val();
    if (paymentType=='buy' || 'payments'){ 
        $('.buy-options input[type="radio"]').prop('disabled', false);
        $('input[type="radio"][value="omniva"]').prop('disabled', true); 
        $('.buy-options input[type="radio"]').prop('checked', false);       
    }
    if (paymentType=='rent'){
        $('.buy-options input[type="radio"]').prop('disabled', true);
        $('input[type="radio"][value="omniva"]').prop('disabled', false);
        $('.buy-options input[type="radio"]').prop('checked', false);
    }
})

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
            $rowText = $row.text().toLowerCase();
            $rowDate = $row.find("td:nth-child(2)").text();

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
