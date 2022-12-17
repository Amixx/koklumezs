var localhosts = ["localhost", "127.0.0.1"];
var origin = window.location.origin;

window.IS_LOCAL = false;
localhosts.forEach(function (host) {
    if (origin.indexOf(host) !== -1) {
        window.IS_LOCAL = true;
    }
});

window.IS_PROD = !IS_LOCAL;

window.getUrl = function (url) {
    var prefix = IS_PROD ? '/sys' : '';
    return prefix + url;
}

window.getFullUrl = function (url) {
    var protocol = IS_LOCAL ? "http" : "https";
    return protocol + "://" + window.location.host + getUrl(url);
}

$(document).ready(function () {
    $('#lessons-sorting-select').on('select2:select', function (e) {
        var href = new URL(window.location.href);
        href.searchParams.set('sortType', e.params.data.id);
        window.location.replace(href.toString());
    });

    if ($('select').length) {
        $('select').select2();
        $('select.tags').select2({
            tags: true,
            insertTag: function (data, tag) {
                // Insert the tag at the end of the results
                data.push(tag);
            }
        });
    }

    $(function () {
        $('img[title]').tooltip()
    });

    if ('serviceWorker' in navigator) {
        if (IS_PROD) {
            navigator.serviceWorker.register(getUrl('/sw.js'), {
                scope: getUrl('/')
            });
        }
    }


    /* When the user scrolls down, hide the navbar. When the user scrolls up, show the navbar */
    var prevScrollpos = window.pageYOffset;
    var $nav = $("#navbar");
    var $chatBtn = $("#chat-btn-with-icons")

    window.onscroll = function () {
        var currentScrollPos = window.pageYOffset;
        if (prevScrollpos > currentScrollPos) {
            $nav.css("top", 0);
            if ($chatBtn) $chatBtn.css("top", "20px");
        } else {
            $nav.css("top", "-90px");
            if ($chatBtn) $chatBtn.css("top", "-110px");
        }
        prevScrollpos = currentScrollPos;
    }

    var deferredPrompt;
    var $installButton = $("#install-prompt");
    var $promtModal = $("#a2hs-modal");
    var $promptClose = $("#a2hs-modal-close");
    var $promptInstall = $("#a2hs-modal-install");

    $promptClose.on('click', function () {
        $promtModal.modal('hide');
    });
    $promptInstall.on('click', function () {
        handleDeferredPrompt();
    });

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;

        if (!$('#myModal').hasClass('in')) $installButton.show();
        $promtModal.modal('show');
    });

    $installButton.on('click', function () {
        $installButton.css("background", "gainsboro");
        handleDeferredPrompt();
    });

    function handleDeferredPrompt() {
        deferredPrompt.prompt();

        deferredPrompt.userChoice.then(function (choiceResult) {
            if (choiceResult.outcome === 'accepted') {
                $installButton.hide();
                $promtModal.modal('hide');
            } else {
                $installButton.css("background", "#007D82");
            }
        });
    }

    $(".ReplyButton").on('click', function () {
        var newText = this.innerText === "Atbildēt" ? "Atcelt" : "Atbildēt";
        this.innerText = newText;
        var commentId = this.id.split("-")[1];
        var responseClass = ".response-" + commentId;
        var responseElement = $(responseClass);
        responseElement.toggleClass("hidden");
    });

    var userNavbarLessonDropdown = document.querySelector(".navbar-lessons-dropdown-toggle a");
    if (userNavbarLessonDropdown) {
        userNavbarLessonDropdown.href = "/lekcijas";
        $(".navbar-lessons-dropdown-toggle a").removeAttr("data-toggle");
    }

    setupArchiveSearchByCategory();
    setupLectureFilterByDifficulty();
    setupAssignUserListFilters();

    $("select[name='SignUpForm[ownsInstrument]']").on('change', function () {
        if ($("span.select2").hasClass('select-warning')) {
            $("span.select2").removeClass("select-warning");
            let div = document.getElementById('has-instrument');
            div.removeChild(div.lastChild);
        }
        if (parseInt(this.value) === 1) {
            $("div.has-experience").addClass("active");
        } else {
            $("div.has-experience").removeClass("active");
        }
    });

    addPopoverToElement(
        ".info-school-email",
        "<p>Skolas e-pasts tiek izmantots ziņojumu nosūtīšanai skolēniem, kā arī uz šo e-pastu tiek sūtīti paziņojumi.</p>"
    );

    $("#sentinvoices-paid_date").attr("autocomplete", "off");

    setupVideoPlayers();
    setupPostRegistrationModal();
    firstLessonEvaluateLessonModal();

    if (window.manualLectures) {
        setupLessonAssignmentAutomaticMessages();
    }

    setupPayments();

    $planSuggestionModal = $("#plan-suggestion-modal");
    if ($planSuggestionModal) $planSuggestionModal.modal("show");


    $('.fitness-toggle-technique-vid').on('click', function () {
        $(this).parent().next().toggleClass('hidden')
    });

    setupInterchangeableExerciseSelects();
    setUpTimer();
    setupNextReplacementExerciseButton();
    setupInsertProgressionChainExerciseButton();
});

function setupInterchangeableExerciseSelects() {
    $('select#interchangeable-exercises, select.all-exercise-select').select2({
        minimumInputLength: 3,
        ajax: {
            url: getUrl('/fitness-exercises/for-select'),
            delay: 250,
            dataType: 'json',
        },
    });
}


function increaseIndexForSelectEl(selectEl, oldIndex, newIndex) {
    selectEl.html(
        selectEl.html()
            .replaceAll("[" + oldIndex + "]", "[" + newIndex + "]")
            .replaceAll("-" + oldIndex + "-", "-" + newIndex + "-")
    );
}

function increaseIndexBy1(buttonEl, oldIndex) {
    var $selectEl = $(buttonEl).parent();
    var $percentEl = $selectEl.next();
    var newIndex = oldIndex + 1;
    increaseIndexForSelectEl($selectEl, oldIndex, newIndex);
    if ($percentEl.length) increaseIndexForSelectEl($percentEl, oldIndex + 1, newIndex + 1);
}

function setupInsertProgressionChainExerciseButton() {
    var $buttons = $('.btn-insert-progression-chain-exercise');
    $buttons.on('click', function () {
        $("body").find("select").select2("destroy");

        var buttonIndex = $buttons.index($(this));

        var selectContainerClone = this.parentElement.cloneNode(true);
        var percentInputClone = this.parentElement.nextSibling.nextSibling.cloneNode(true);

        increaseIndexForSelectEl($(selectContainerClone), buttonIndex, buttonIndex + 1);
        $(selectContainerClone).find("select").select2().val(null).trigger("change");
        $(selectContainerClone).insertAfter($(this.parentElement));

        $(percentInputClone).html($(percentInputClone).html().replaceAll(buttonIndex - 1, buttonIndex - 1));
        $(percentInputClone).find("input").val(null);
        $(percentInputClone).insertAfter($(this.parentElement));

        $buttons.each(function (i, el) {
            if (i === buttonIndex) {
                var $percentEl = $(el).parent().next().next().next();
                if ($percentEl) increaseIndexForSelectEl($percentEl, i + 1, i + 2);
            } else if (i > buttonIndex) increaseIndexBy1(el, i);
        });
        var $lastSelect = $('.exercise-select-container').last();
        increaseIndexForSelectEl($lastSelect, $buttons.length, $buttons.length+1);
        $buttons = $('.btn-insert-progression-chain-exercise');

        $("body").find("select").select2();
    })
}


function setupNextReplacementExerciseButton() {
    var $nextReplacementeExerciseBtn = $('.btn-next-replacement-exercise');
    if (!$nextReplacementeExerciseBtn) return;

    $nextReplacementeExerciseBtn.on('click', function () {
        var $replacementOptionContainer = $(this).closest('.exercise-replacement-option-container');
        $replacementOptionContainer.attr('hidden', true);
        $replacementOptionContainer.next().attr('hidden', false);
    })
}


function formatTimeLeft(time) {
    var minutes = Math.floor(time / 60);
    var seconds = time % 60;
    if (seconds < 10) {
        seconds = "0" + seconds;
    }
    return minutes + ":" + seconds;
}

function timeStringToSeconds(timeString) {
    var split = timeString.split(':');
    if (split.length === 1) return parseInt(split[0]);

    return (parseInt(split[0]) * 60) + parseInt(split[1]);
}

function setUpTimer() {
    var $timer = $('.base-timer');
    if (!$timer.length) return;

    var $timerLabel = $timer.find('.base-timer__label');
    var $pathRemaining = $timer.find('.base-timer__path-remaining');
    var timeString = $timerLabel.text();

    var TIME_LIMIT = timeStringToSeconds(timeString);

    var timePassed = 0;
    var timeLeft = TIME_LIMIT;

    var FULL_DASH_ARRAY = 283;

    function setCircleDasharray() {
        var firstArg = (((timeLeft / TIME_LIMIT) * FULL_DASH_ARRAY) - 10).toFixed(0);
        if (firstArg < 0) firstArg = 0;
        $pathRemaining.attr("stroke-dasharray", firstArg + " " + FULL_DASH_ARRAY);
    }

    var timerInterval = setInterval(() => {
        timePassed = timePassed += 1;
        timeLeft = TIME_LIMIT - timePassed;
        $timerLabel.text(formatTimeLeft(timeLeft));
        if (timeLeft === 0) {
            $('.exercise-action-btn')[0].click();
            clearInterval(timerInterval);
        }

        setCircleDasharray();
    }, 1000);
}


function createPaymentIntent(planId, priceType, callback) {
    var data = {
        plan_id: planId,
        price_type: priceType,
    }

    $.ajax({
        url: getUrl("/payment/generate-payment-intent"),
        type: "POST",
        data: data,
        success: function (res) {
            callback(res);
        }
    });
}


function setupPayments() {
    var elements = null;
    var paymentElement = null;
    var stripe = null;

    var $checkoutBtn = $(".PlanSuggestion__CheckoutButton");
    var $checkoutContainer = $(".PlanSuggestion__Payment");
    var $confirmPlanPurchaseBtn = $(".PlanSuggestion__ConfirmPaymentButton");
    var $confirmInvoicePaymentBtn = $(".ConfirmInvoicePayment");
    var $paymentSpinner = $("#payment-spinner");
    var $cancelBtn = $(".PlanSuggestion__CancelPayment");

    $checkoutBtn.on("click", function () {
        $paymentSpinner.show();

        var $planSuggestion = $(this).closest(".PlanSuggestion");
        var planIdForPayment = $planSuggestion.data("planId");
        var priceType = $(this).data('priceType');

        createPaymentElement(planIdForPayment, priceType, function (els, el) {
            elements = els;
            paymentElement = el;
        });

        $(".PlanSuggestion").hide();
        $(this).closest(".PlanSuggestion").show();
        $checkoutContainer.show();
        $(this).closest(".PlanSuggestion__CheckoutButton").hide();
        $('.PlanSuggestion__CheckoutButton:visible').parent().hide();
    });

    $cancelBtn.on("click", function () {
        $(".PlanSuggestion").show();
        $(".PlanSuggestion__CheckoutButton").show();
        $(".PlanSuggestion__Option").show();
        $checkoutContainer.hide();
        $(".PlanSuggestion input").prop('disabled', false);
        paymentElement.destroy();
        $cancelBtn.hide();
    });

    $confirmPlanPurchaseBtn.on("click", function () {
        if (!elements) return;
        $paymentSpinner.show();
        $(".PlanSuggestion__PaymentInner").hide();

        var $planSuggestion = $(".PlanSuggestion:visible");
        var planIdForPayment = $planSuggestion.data("planId");
        var allAtOnce = $('.PlanSuggestion__Option:visible').hasClass('single');
        var returnUrl = "/payment/success?planId=" + planIdForPayment + "&allAtOnce=" + allAtOnce;

        handleConfirmPaymentClick(returnUrl);
    });

    $confirmInvoicePaymentBtn.on('click', function () {
        var returnUrl = "/sent-invoices/handle-payment-success?invoice_id=" + $(this).data().invoiceId;

        handleConfirmPaymentClick(returnUrl);
    });

    function handleConfirmPaymentClick(returnUrl) {
        if (!elements) return;
        $paymentSpinner.show();
        $(".PlanSuggestion__PaymentInner").hide();

        stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: getFullUrl(returnUrl),
            },
        }).then(function (result) {
            if (result.error) {
                $paymentSpinner.hide();
                $("#payment-error").show();
                $("#payment-error-message").text(result.error.message);
                $("#payment-error-code").append(result.error.code);
            }
        });
    }

    $(".PlanSuggestion__RetryPaymentButton").on('click', function () {
        $("#payment-error").hide();
        $(".PlanSuggestion__PaymentInner").show();
    });


    var $checkoutModal = $("#checkout-modal");
    var $paymentLinks = $(".payment-link");
    if (!$paymentLinks.length) return;

    $checkoutModal.on('hidden.bs.modal', function (e) {
        if (paymentElement) {
            paymentElement.destroy();
            paymentElement = null;
        }
    });

    $paymentLinks.on("click", function () {
        $checkoutModal.modal('show');
        $paymentSpinner.show();
        stripe = Stripe(window.stripeConfig.pk);
        var $buttonContainer = $(".PlanSuggestion__ButtonContainer");
        var invoiceId = $(this).data().invoiceId;

        $confirmInvoicePaymentBtn.data('invoiceId', invoiceId);

        prepareInvoicePayment(invoiceId, function (res) {
            var paymentIntent = JSON.parse(res);

            if (!elements) {
                elements = stripe.elements({
                    clientSecret: paymentIntent.client_secret,
                    locale: window.userLanguage,
                });
            }

            paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');
            paymentElement.on('ready', function () {
                $paymentSpinner.hide();
                paymentElement.focus();
            });

            paymentElement.on('change', function (event) {
                if (event.complete) {
                    $buttonContainer.show();
                } else {
                    $buttonContainer.hide();
                }
            });
        });
    });


    function createPaymentElement(planId, priceType, callback) {
        stripe = Stripe(window.stripeConfig.pk);
        var $buttonContainer = $(".PlanSuggestion__ButtonContainer");

        return createPaymentIntent(planId, priceType, function (res) {
            var paymentIntent = JSON.parse(res);
            var elements = stripe.elements({
                clientSecret: paymentIntent.client_secret,
                locale: window.userLanguage,
            });

            var paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');
            paymentElement.on('ready', function () {
                $paymentSpinner.hide();
                $cancelBtn.show();
                paymentElement.focus();
            });

            paymentElement.on('change', function (event) {
                if (event.complete) {
                    $buttonContainer.show();
                } else {
                    $buttonContainer.hide();
                }
            });

            callback(elements, paymentElement);
        });
    }

    function prepareInvoicePayment(id, callback) {
        $.ajax({
            url: getUrl("/payment/prepare-invoice-payment"),
            type: "POST",
            data: {invoice_id: id},
            success: callback
        });
    }
}


function setupLessonAssignmentAutomaticMessages() {
    var $lessonSelect = $("select[name='UserLectures[lecture_id]']");
    var $saveMessageBlock = $("label[for='saveEmail']");
    var $updateMessageBlock = $("label[for='updateEmail']");
    var $subjectInput = $("input[name='subject']");
    var $messageBodyInput = $("textarea[name='teacherMessage']");

    $lessonSelect.on("change", function () {
        var assignmentMessage = window.manualLectures[this.value].assignmentMessage;
        var newTitle = "";
        var newMessageBody = "";

        if (assignmentMessage) {
            if (assignmentMessage.title) newTitle = assignmentMessage.title;
            if (assignmentMessage.text) newMessageBody = assignmentMessage.text;
            showUpdateMessageBlock();
        } else {
            showSaveMessageBlock();
        }

        $subjectInput.val(newTitle);
        $messageBodyInput.val(newMessageBody);
    });

    function showSaveMessageBlock() {
        $saveMessageBlock.show();
        $updateMessageBlock.hide();
        resetCheckboxes();
    }

    function showUpdateMessageBlock() {
        $saveMessageBlock.hide();
        $updateMessageBlock.show();
        resetCheckboxes();
    }

    function resetCheckboxes() {
        $saveMessageBlock.find("input[type='checkbox']").prop('checked', false);
        $updateMessageBlock.find("input[type='checkbox']").prop('checked', false);
    }
}

function addPopoverToElement($selector, html) {
    $($selector).popover({
        html: true,
        placement: 'bottom',
        trigger: 'hover',
        content: function () {
            return html;
        }
    });
}

function setupArchiveSearchByCategory() {
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

    function addCheckboxEventListener(selector) {
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

function setupLectureFilterByDifficulty() {
    $("#assign-page-main .select2.select2-container").on("click", function () {
        var preferredDifficulty = $("#PreferredLectureDifficulty").val()
        if (!preferredDifficulty) {
            return;
        }

        $("li.select2-results__option").each(function () {
            if (!this.innerText) {
                return;
            }

            var parts = this.innerText.split("(");
            var difficulty = parseInt(parts[parts.length - 1].replace(")", "").trim());

            if (difficulty !== parseInt(preferredDifficulty)) {
                $(this).css("display", "none");
                $(document).scroll();
            }
        })
    })
}

var SUB_TYPES = {
    free: "free",
    paid: "paid",
    lead: "lead",
    pausing: "pausing"
}

function setupAssignUserListFilters() {
    var $langElems = $("td.user-language");
    setupAssignFilterByLanguage($langElems);
    setupAssignFilterBySubscriptionType($langElems);

    setupNeedHelpButton();
}

function getSubTypeSelector(type) {
    return ".subscription-type-selector.type-" + type;
}

var subTypeSelectors = Object.keys(SUB_TYPES).map(function (subType) {
    return getSubTypeSelector(subType);
}).join(", ");

function getSelectedSubTypes() {
    var subTypes = [];

    if ($(getSubTypeSelector(SUB_TYPES.free)).prop("checked")) subTypes.push("free");
    if ($(getSubTypeSelector(SUB_TYPES.paid)).prop("checked")) subTypes.push("paid");
    if ($(getSubTypeSelector(SUB_TYPES.lead)).prop("checked")) subTypes.push("lead");
    if ($(getSubTypeSelector(SUB_TYPES.pausing)).prop("checked")) subTypes.push("pausing");

    return subTypes;
}


function setupAssignFilterByLanguage($langElems) {
    $("#UserLanguageSelector").on("change", function () {
        var lang = this.value;
        var subTypes = getSelectedSubTypes();

        modifyViewHrefOnFilter("lang", lang);
        filterAssignStudentList($("#UserLanguageSelector").val(), $langElems, subTypes);
    });
}

function setupAssignFilterBySubscriptionType($langElems) {
    $(subTypeSelectors).on("change", function () {
        var subTypes = getSelectedSubTypes();

        modifyViewHrefOnFilter("subTypes", subTypes.join(","));
        filterAssignStudentList($("#UserLanguageSelector").val(), $langElems, subTypes);
    });

    $(getSubTypeSelector(SUB_TYPES.free)).prop("checked", true);
    $(getSubTypeSelector(SUB_TYPES.paid)).prop("checked", true);
    $(getSubTypeSelector(SUB_TYPES.lead)).prop("checked", true);
}

function filterAssignStudentList(lang, $langElems, subTypes) {
    $("#AssignTable tr").each(function () {
        this.style.display = "";
    });

    $langElems.each(function () {
        var $this = $(this);
        var $subTypeElem = $this.next();
        var langText = $this.text();
        var subTypeText = $subTypeElem.text();

        if (shouldHideRow(lang, langText, subTypes, subTypeText)) {
            this.parentElement.style.display = "none";
        }
    });
}

function shouldHideRow(lang, langText, subTypes, subTypeText) {
    var hideRow;
    var showAllSubTypes = subTypes.length === 0 || subTypes.length === 4;

    if (lang === "all") {
        if (showAllSubTypes) {
            hideRow = false;
        } else {
            hideRow = subTypes.indexOf(subTypeText) === -1;
        }
    } else {
        if (showAllSubTypes) {
            hideRow = lang !== langText;
        } else {
            hideRow = lang !== langText || subTypes.indexOf(subTypeText) === -1;
        }
    }

    return hideRow;
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

function reloadChat(message, clearChat, showSpinner) {
    var url = $(".btn-send-comment").data("url");
    var model = $(".btn-send-comment").data("model");
    var recipient_id = $(".btn-send-comment").data("recipient_id");

    if (showSpinner) hideChatContent();

    $.ajax({
        url: url,
        type: "POST",
        data: {message: message, model: model, recipient_id: recipient_id},
        success: function (data) {
            loadUnreadMessagesCount();

            data = JSON.parse(data);
            if (clearChat) $("#chat_message").val("");
            $("#chat-box").html(data.content);
            if (data.userList) {
                $("#chat-user-list").html(data.userList);
            }

            showChatContent();
        }
    });
}

function scrollChatToBottom() {
    var useTimeout = false;
    $("#chat-box-container").scrollTop(function () {
        useTimeout = this.scrollHeight === 0;
        return this.scrollHeight;
    });

    //šitā nekad nevajag darīt!!! :)
    if (useTimeout) {
        setTimeout(function () {
            $("#chat-box-container").scrollTop(function () {
                return this.scrollHeight;
            });
        }, 200);
    }
}

var $unreadMessagesCount = $(".chat-unread-count");
var $unreadConversationsCount = $(".chat-unread-count-groups");

function loadUnreadMessagesCount() {
    var url = IS_PROD ? "/sys/chat/get-unread-count" : "/chat/get-unread-count";

    $unreadMessagesCount.hide();
    $unreadConversationsCount.hide();

    $.ajax({
        url: url,
        type: "POST",
        success: function (jsonData) {
            var data = JSON.parse(jsonData);

            if (parseInt(data.messages) > 0) {
                $unreadMessagesCount.html(data.messages);
                $unreadMessagesCount.show();
            } else {
                $unreadMessagesCount.hide();
            }

            if (parseInt(data.conversations) > 1) {
                $unreadConversationsCount.html(data.conversations);
                $unreadConversationsCount.show();
            } else {
                $unreadConversationsCount.hide();
            }
        }
    });
}

function isChatOpen() {
    return $("#chatModal:visible").length > 0;
}


setInterval(function () {
    if (isChatOpen()) reloadChat('', false, false);
}, 600000); // katru minūti

setInterval(function () {
    if (!isChatOpen()) loadUnreadMessagesCount();
}, 120000); // katru otro minūti

$(".btn-send-comment").on("click", function () {
    var message = $("#chat_message").val();
    $(".chat-unread-count").hide();
    reloadChat(message, true, false);
});

$("#chat-toggle-button").on('click', function () {
    reloadChat("", true, true);
});

$(document).on('click', ".chat-user-item", function () {
    var newRecipientId = $(this).data("userid");
    $(".btn-send-comment").data("recipient_id", newRecipientId);

    reloadChat("", true, true);
});

$(document).on('click', ".chat-with-student", function () {
    var newRecipientId = $(this).data("userid");
    $(".btn-send-comment").data("recipient_id", newRecipientId);

    reloadChat("", true, true);
    $("#chatModal").modal('show');
});

var $chatSpinner = $("#chat-spinner");
var $chatContent = $("#chat-content-container");

function hideChatContent() {
    $chatContent.hide();
    $chatSpinner.show();
}

function showChatContent() {
    $chatContent.show();
    $chatSpinner.hide();
    scrollChatToBottom();
}

$("#export-sent-invoices").on("click", exportSentInvoices);

function exportSentInvoices() {
    var csvContent = "\uFEFF";

    var $rows = $("#sent-invoices-table tr:visible:not(:first-child)");
    $rows.each(function (i, row) {
        var r = [];
        var $cols = $(row).find("td:not(:first-child)");
        $cols.each(function (i, col) {
            r.push(col.innerText);
        });
        csvContent += r.join(",") + "\r\n";
    });

    exportToCSV(csvContent, 'dowload.csv', 'text/csv;encoding:utf-8,%EF%BB%BF');
}

var exportToCSV = function (content, fileName, mimeType) {
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

function fiterSentInvoices() {
    var searchValue = $("input[name='sent-invoices-filter']").val().toLowerCase();
    var year = $('#invoices-year-selector').select2().val();
    var month = $('#invoices-month-selector').select2().val();

    var filterByText = searchValue.length >= 4;
    var filterByDate = year !== "" && month !== "";

    if (filterByDate) {
        year = parseInt(year);
        month = parseInt(month);

        var firstDay = new Date(year, month, 1);
        var lastDay = new Date(year, month + 1, 0);

        var firstDayDate = makeDateString(firstDay);
        var lastDayDate = makeDateString(lastDay);
    }


    if (filterByText || filterByDate) {
        $sentInvoicesRows.each(function (i, row) {
            var $row = $(row);
            var $rowText = $row.text().toLowerCase();
            var $rowDate = $row.find("td:nth-child(2)").text();

            if (filterByText && $rowText.indexOf(searchValue) === -1) $row.hide();
            else if (filterByDate && $rowDate < firstDayDate || $rowDate > lastDayDate) {
                $row.hide()
            } else $row.show();
        });
    } else {
        $sentInvoicesRows.show();
    }
}

function makeDateString(date) {
    return date.getFullYear()
        + '-' + leadingZero(date.getMonth() + 1)
        + '-' + leadingZero(date.getDate())
}

function leadingZero(string) {
    return ('0' + String(string)).slice(-2);
}

$("[data-role=fitness-eval-btn]").on("click", submitFitnessEvaluation);

function submitFitnessEvaluation() {
    var $hiddenInput = $(this).parent().parent().siblings("[name=difficulty-evaluation]");
    $hiddenInput.val(this.dataset.value);
    if(!this.dataset.isCouldNotFinish) {
        $(this).closest("form").submit();
    } else {
        $('#exercise-could-not-finish-modal').modal('show');
    }
}

$("#could-not-finish-modal-submit-button").on('click', function(){
    this.disabled = true
    $('input[name="executed-reps"]').val($('#could-not-finish-modal-reps-input').val())
    $('.fitness-difficulty-eval').closest('form').submit()
})

$("[data-role=evaluation-emoji]").on("click", submitLessonEvaluation);

function submitLessonEvaluation() {
    var $hiddenInput = $(this.parentElement).siblings("[name=difficulty-evaluation]");
    $hiddenInput.val(this.dataset.value);
    $(this).closest("form").submit();
}

$("[id^=lesson_modal]").on("hidden.bs.modal", pausePlayerOnModalClose);

function pausePlayerOnModalClose() {
    var $pauseBtn = $(this).find(".plyr__control.plyr__control--overlaid.plyr__control--pressed");
    if ($pauseBtn) $pauseBtn.trigger('click');
}


function setupVideoPlayers() {
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

    Array.from(document.querySelectorAll("[data-role='player']")).forEach(function (video) {
        var opts = JSON.parse(JSON.stringify(options))
        if (video.id.includes('fitness_main')) {
            opts.autoplay = true
            opts.youtube = {
                autoplay: true,
            }
        }
        players[video.id] = new Plyr(video, opts);
        players[video.id].poster = posters[video.id];
    });

    window.players = players;
}


function setupNeedHelpButton() {
    var $message = $("#need-help-message");
    var $error = $("#need-help-error");
    var $submitButton = $("#submit-need-help-message");
    var endpointUrl = getUrl("/need-help-message/create");

    $error.hide();

    $message.on('input', function () {
        if ($error.is(":visible")) {
            $error.hide();
        }
    });


    $submitButton.on('click', function () {
        var message = $message[0].value;
        if (!message) {
            $error.show();
            return;
        }

        var lessonId = $(this).data("lessonid");

        $submitButton.button('loading');
        $.ajax({
            url: endpointUrl,
            type: "POST",
            data: {message: message, lessonId: lessonId},
            success: function (res) {
                $submitButton.button('reset');
                $("#need-help-modal").modal('hide');
                $message[0].value = "";
            }
        });
    })
}

function setupPostRegistrationModal() {
    var $modal = $("#post-registration-modal");
    if ($modal) {
        $modal.modal('show');
        var $btnContainer = $("#post-registration-modal-buttons")
        var $startInstantlyBtn = $("#btn-start-instantly");
        var $startLaterBtn = $("#btn-start-later");
        var $startLaterForm = $("#start-later-form");
        $startLaterForm.hide();

        $startInstantlyBtn.on('click', function () {
            $startInstantlyBtn.button('loading');
        });

        $startLaterBtn.on('click', function () {
            $startLaterForm.show();
            $btnContainer.hide();
        });
    }
}

function firstLessonEvaluateLessonModal() {
    if (window.isRegisteredAndNewLesson) {
        var ul = document.getElementById("navbar-collapse").getElementsByTagName("li");
        for (var i = 0; i < ul.length - 1; i++) {
            ul[i].addEventListener("click", function (event) {
                event.preventDefault();
                $("#alertEvaluation-next-lesson").modal("show");
            });
        }
    }
}

$("#signup-questions-add-answer").on('click', function () {
    var $addedQuestionsContainer = $("#signup-questions-answer-choices");
    var $addedQuestions = $addedQuestionsContainer.find(".form-group");
    var addedQuestionsCount = $addedQuestions.length;

    var $newQuestion = $($addedQuestions[0]).clone();
    $newQuestion.find("input").attr('name', 'answer_choice[' + addedQuestionsCount + ']');
    $addedQuestionsContainer.append($newQuestion);
});