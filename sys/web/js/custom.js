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
        if(parseInt(this.value) === 0){
            $("div.has-experience").removeClass("active");
        }else {
            $("div.has-experience").addClass("active");
        }
    });

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

function reloadChat(message, clearChat) {
    var url = $(".btn-send-comment").data("url");
    var model = $(".btn-send-comment").data("model");
    var recipient_id = $(".btn-send-comment").data("recipient_id");

    $.ajax({
        url: url,
        type: "POST",
        data: {message: message, model: model, recipient_id: recipient_id},
        success: function (data) {
            if (clearChat) $("#chat_message").val("");
            $("#chat-box").html(data);
        }
    });
}

var $unreadCount = $(".chat-unread-count");

function loadUnreadMessagesCount() {
    var url = "/chat/get-unread-count";

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
    if(isChatOpen()) reloadChat('', false);
}, 5000); // katru piekto sekundi

setInterval(function () {
    if(!isChatOpen()) loadUnreadMessagesCount();
}, 60000); // katru minūti

$(".btn-send-comment").on("click", function () {
    var message = $("#chat_message").val();
    $(".chat-unread-count").hide();
    reloadChat(message, true);
});


$("#chat-toggle-button").on('click', function(){
    $unreadCount.hide();

    var url = "/user/open-chat";
    $.ajax({
        url: url,
        type: "POST",
    });
});

var $sentInvoicesRows = $("#sent-invoices-table tbody tr");
$("input[name='sent-invoices-filter']").on("input", function(){
    var searchValue = this.value.toLowerCase();
    if(searchValue.length >= 4){
        $sentInvoicesRows.each(function(i, row){
            var $row = $(row);
            $rowText = $row.text().toLowerCase();
            if($rowText.indexOf(searchValue) === -1) $row.hide();
            else $row.show();
        });
    }else{
        $sentInvoicesRows.show();
    }
});