var totalTodo = 0;
var completedItems = 0;
var currentEditElement = null;

$(document).ready(function() {
    totalTodo = $("#totalTodo").val();
    completedItems = $("#completedItems").val();

    hideShowItemOptions("#itemsRemaining", totalTodo);
    hideShowItemOptions("#clearCompleteItems", completedItems);

    $("#addTask").keypress(function(event) {
        if (event.keyCode === 13) {
            var value = $("#addTask").val();
            if (!value) {
                return;
            }

            $.ajax({
                type: "POST",
                url: "index/addTodo/",
                data: {
                    text: value,
                    status: false
                },
                async: false,
                success: function(data) {
                    buildAfterInsertDiv(data.id, value);
                },
                error: function(data) {
                    alert(data.responseJSON.error);
                }
            });
        }
    });

    $(document).on('dblclick', '.todo-span', function() {
        if (currentEditElement) {
            buildDivAfterEdit(currentEditElement.parentElem, currentEditElement.value, currentEditElement.checkedValue);
        }

        var value = $(this).text();
        var $parentElem = $(this).parent();

        currentEditElement = {
            value: value,
            parentElem: $parentElem,
            checkedValue: $(this).siblings('.checker').find('.checkbox').is(":checked")
        };

        $parentElem.empty();

        $parentElem.addClass('p-tb-10');
        var div = '<div class="checker"></div>' +
            '<input id="editTask" type="text" class="form-control edit-todo" value="' + value + '">';
        $parentElem.append(div);
    });

    $(document).on('keypress', '#editTask', function(event) {
        if (event.keyCode === 13) {
            var value = $("#editTask").val();
            if (!value || !currentEditElement) {
                return;
            }
            currentEditElement.value = value;

            updateTodoValue(
                currentEditElement.parentElem.parent().data('id'),
                currentEditElement.value,
                currentEditElement.checkedValue,
                function() {
                    buildDivAfterEdit(currentEditElement.parentElem, currentEditElement.value, currentEditElement.checkedValue);
                }
            );

            currentEditElement = null;
        }
    });

    $(document).on('change', '.checkbox', function() {
        var status = $(this).prop('checked');
        var topParent = $(this).parents().eq(3);
        var text = $(this).parents().eq(1).siblings('.todo-span').text();

        updateTodoValue(
            topParent.data('id'),
            text,
            status,
            function() {
                rebuildDataAfterCheckboxClicked(status, topParent);
            }
        );
    });

    $(document).on('click', '.remove-todo-item', function() {
        var $parentElem = $(this).parents().eq(1);
        removeTodoItems(
            [$parentElem.data('id')], [$parentElem],
            true
        );
    });

    $("#clearCompleteItems").click(function() {
        var elements = [];
        var deleteIds = [];
        $(".task-complete").each(function() {
            elements.push(this);
            deleteIds.push($(this).data('id'));
        });
        removeTodoItems(deleteIds, elements);

        completedItems = 0;
        hideShowItemOptions("#clearCompleteItems", completedItems);
    });

    $("#all").click(function() {
        if ($(this).find("a").hasClass("option-selected")) {
            return;
        }

        $("#active a").removeClass("option-selected");
        $("#completed a").removeClass("option-selected");

        $("#all a").addClass("option-selected");

        $(".todo-list").each(function() {
            $(this).show();
        });
    });

    $("#active").click(function() {
        if ($(this).find("a").hasClass("option-selected")) {
            return;
        }

        $("#all a").removeClass("option-selected");
        $("#completed a").removeClass("option-selected");

        $("#active a").addClass("option-selected");

        $(".todo-list").each(function() {
            if ($(this).hasClass('task-complete')) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });

    $("#completed").click(function() {
        if ($(this).find("a").hasClass("option-selected")) {
            return;
        }
        $("#all a").removeClass("option-selected");
        $("#active a").removeClass("option-selected");

        $("#completed a").addClass("option-selected");

        $(".todo-list").each(function() {
            if (!$(this).hasClass('task-complete')) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
});

function hideShowItemOptions(selector, count) {
    if (count > 0) {
        $(selector).removeClass('hide');
    } else {
        $(selector).addClass('hide');
    }
}

function buildAfterInsertDiv(id, value) {
    var todoListDiv = '<div data-id="' + id + '"class="todo-list">' +
        '<div class="todo-item">' +
        '<div class="checker"><span><input class="checkbox" type="checkbox"></span></div> ' +
        '<span class="todo-span">' + value + '</span>' +
        '<a href="#" class="float-right remove-todo-item"><i class="fa fa-times"></i></a>' +
        '</div>' +
        '</div>';

    todoListDiv = $(todoListDiv);
    $("#listDiv").append(todoListDiv);
    $("#addTask").val("");

    totalTodo++;
    hideShowItemOptions("#itemsRemaining", totalTodo);
    $("#itemCount").text(totalTodo);
    var currentSelectOption = $(".option-selected").parent().attr('id');
    if (currentSelectOption == 'completed') {
        todoListDiv.hide();
    }
}

function buildDivAfterEdit(element, spanValue, checkboxValue) {
    element.empty();
    var checked = checkboxValue ? 'checked' : '';
    var div = '<div class="checker"><span><input class="checkbox" type="checkbox" ' + checked + '></span></div> ' +
        '<span class="todo-span">' + spanValue + '</span>' +
        '<a href="#" class="float-right remove-todo-item"><i class="fa fa-times"></i></a>';

    element.append(div);
    element.removeClass('p-tb-10');
}

function updateTodoValue(id, text, status, afterUpdateMethod) {
    $.ajax({
        type: "PUT",
        url: "index/updateTodo/" + id,
        data: {
            text: text,
            status: status
        },
        async: false,
        success: function(data) {
            afterUpdateMethod();
        },
        error: function(data) {
            alert(data.responseJSON.error);
        }
    });
}

function rebuildDataAfterCheckboxClicked(status, parentElement) {
    if (status) {
        parentElement.addClass('task-complete');
        completedItems++;
        hideShowItemOptions("#clearCompleteItems", completedItems);

        totalTodo--;
        hideShowItemOptions("#itemsRemaining", totalTodo);

        var currentSelectOption = $(".option-selected").parent().attr('id');
        if (currentSelectOption == 'active') {
            parentElement.hide();
        }
    } else {
        parentElement.removeClass('task-complete');
        completedItems--;
        hideShowItemOptions("#clearCompleteItems", completedItems);

        totalTodo++;
        hideShowItemOptions("#itemsRemaining", totalTodo);

        var currentSelectOption = $(".option-selected").parent().attr('id');
        if (currentSelectOption == 'completed') {
            parentElement.hide();
        }
    }

    $("#itemCount").text(totalTodo);
}

function removeTodoItems(ids, removeElements, isSingleRemove = false) {
    $.ajax({
        type: "DELETE",
        url: "index/deleteTodo/",
        data: {
            ids: ids
        },
        async: false,
        success: function(data) {
            if (isSingleRemove) {
                deleteTodoFromList(removeElements[0]);
            }

            removeElements.forEach(function(item) {
                $(item).remove();
            });
        },
        error: function(data) {
            alert(data.responseJSON.error);
        }
    });
}

function deleteTodoFromList(element) {
    if (element.hasClass('task-complete')) {
        completedItems--;
        hideShowItemOptions("#clearCompleteItems", completedItems);
    } else {
        totalTodo--;
        hideShowItemOptions("#itemsRemaining", totalTodo);
    }
    $("#itemCount").text(totalTodo);
}