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
    $("#UserSubscriptionTypeSelector").on("change", function () {
        var subType = this.value;
        modifyViewHrefOnFilter("subType", subType);
        filterAssignStudentList($("#UserLanguageSelector").val(), subType);
    });
}

function filterAssignStudentList(lang, subType){
    $("#AssignTable tr").each(function () {
        this.style.display = "";
    });

    var $langElems = $("td.user-language");
    $langElems.each(function () {
        var $subTypeElem = $(this).next()[0];
        var langText = this.innerText;
        var subTypeText = $subTypeElem.innerText;
       
        if (shouldHideRow(lang, langText, subType, subTypeText)) {
            this.parentElement.style.display = "none";
        }
    });

    function shouldHideRow(lang, langText, subType, subTypeText){
        var hideRow;
        if (lang === "all") {
            if (subType === "all") {
                hideRow = false;
            } else {
                hideRow = subType !== subTypeText;
            }
        } else {
            if (subType === "all") {
                hideRow = lang !== langText;
            } else {
                hideRow = lang !== langText || subType !== subTypeText;
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