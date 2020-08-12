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

    makeNavbarMultilineForStudents();

    setupLectureFilterByDifficulty();

    setupAssignFilterByLanguage();

    setupAssignFilterBySubscriptionType();
});

function makeNavbarMultilineForStudents(){
    var navbarItemsSelector = ".navbar-nav.for-students li a";

    $(navbarItemsSelector).each(function (_, item) {
        makeItemMultiline(item);       
    });

    function makeItemMultiline(item){
        item.innerHTML = textToMultiline(item.innerText.split("/"));;
        item.style.lineHeight = "10px";
    }

    function textToMultiline(parts){
        var newText = "";

        parts.forEach(function(part){
            newText += "<p>" + part + "</p>";
        });

        return newText;
    }
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

function setupAssignFilterByLanguage(){
    $("#UserLanguageSelector").on("change", function(){
        var lang = this.value;
        modifyViewHrefOnFilter("lang", lang);
        filterAssignStudentList(lang, $("#UserSubscriptionTypeSelector").val());
    });    
}

function setupAssignFilterBySubscriptionType() {
    // $("#UserSubscriptionTypeSelector").on("change", function () {
    //     var subType = this.value;
    //     modifyViewHrefOnFilter("subType", subType);
    //     filterAssignStudentList($("#UserLanguageSelector").val(), subType);
    // });
    
    
    $(".subscription-type-selector.type-free").on("change", function () {
        // var type = this.classList[1].split("-")[1];
        var subTypes = this.checked ? ["free"] : [];
        if ($(".subscription-type-selector.type-paid").prop("checked")) subTypes.push("paid");
        if ($(".subscription-type-selector.type-lead").prop("checked")) subTypes.push("lead");
        if ($(".subscription-type-selector.type-pausing").prop("checked")) subTypes.push("pausing");

        modifyViewHrefOnFilter("subTypes", subTypes.join(","));
        filterAssignStudentList($("#UserLanguageSelector").val(), subTypes);
    });
    $(".subscription-type-selector.type-paid").on("change", function () {
        // var type = this.classList[1].split("-")[1];
        var subTypes = this.checked ? ["paid"] : [];
        if ($(".subscription-type-selector.type-free").prop("checked")) subTypes.push("free");
        if ($(".subscription-type-selector.type-lead").prop("checked")) subTypes.push("lead");
        if ($(".subscription-type-selector.type-pausing").prop("checked")) subTypes.push("pausing");

        modifyViewHrefOnFilter("subTypes", subTypes.join(","));
        filterAssignStudentList($("#UserLanguageSelector").val(), subTypes);
    });
    $(".subscription-type-selector.type-lead").on("change", function () {
        // var type = this.classList[1].split("-")[1];
        var subTypes = this.checked ? ["lead"] : [];
        if ($(".subscription-type-selector.type-paid").prop("checked")) subTypes.push("paid");
        if ($(".subscription-type-selector.type-free").prop("checked")) subTypes.push("free");
        if ($(".subscription-type-selector.type-pausing").prop("checked")) subTypes.push("pausing");

        modifyViewHrefOnFilter("subTypes", subTypes.join(","));
        filterAssignStudentList($("#UserLanguageSelector").val(), subTypes);
    });
    $(".subscription-type-selector.type-pausing").on("change", function () {
        // var type = this.classList[1].split("-")[1];
        var subTypes = this.checked ? ["pausing"] : [];
        if ($(".subscription-type-selector.type-paid").prop("checked")) subTypes.push("paid");
        if ($(".subscription-type-selector.type-free").prop("checked")) subTypes.push("free");
        if ($(".subscription-type-selector.type-lead").prop("checked")) subTypes.push("lead");

        modifyViewHrefOnFilter("subTypes", subTypes.join(","));
        filterAssignStudentList($("#UserLanguageSelector").val(), subTypes);
    });

    $(".subscription-type-selector.type-free").trigger("click");
    $(".subscription-type-selector.type-paid").trigger("click");
    $(".subscription-type-selector.type-lead").trigger("click");
}

function filterAssignStudentList(lang, subTypes){
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

    function shouldHideRow(lang, langText, subTypes, subTypeText, isUserPassive){
        var hideRow;
        var showAllSubTypes = subTypes.length === 0 || subTypes.length === 3;
        if (isUserPassive && subTypes.indexOf("pausing") === -1){
            hideRow = true;
        }else if (lang === "all") {
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
}

function modifyViewHrefOnFilter(name, value){
    $("#AssignTable tbody tr td a[title='Apskatīt']").each(function () {
        var hrefParts = this.href.split(/[?&]+/)
        var newHrefPart = value !== "all" ? name+"=" + value : "";
        if (hrefParts.length === 1) {
            this.href += "?" + newHrefPart;
        } else if (hrefParts.length === 2) {            
            if (hrefParts[1].indexOf(name) !== -1) {
                if(newHrefPart){
                    this.href = hrefParts[0] + "?" + newHrefPart;
                }else{
                    this.href = hrefParts[0];
                }          
            } else {
                this.href = this.href + "&" + newHrefPart;
            }
        } else {
            console.log(hrefParts, newHrefPart);
            if (hrefParts[1].indexOf(name) !== -1) {
                if(newHrefPart){
                    this.href = hrefParts[0] + "?" + newHrefPart + "&" + hrefParts[2];
                }else{
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