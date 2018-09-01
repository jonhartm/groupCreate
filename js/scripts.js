// When the button to create groups is pressed
// Shuffles the global var STUDENT_LIST, then calls functions to parcel out
// students to groups and display them in the div on the page
$("#create_groups").click(function() {
  // Empty the contents of the current div
  $('#group_div_container').empty();

  // Shuffle the student list
  shuffle(STUDENT_LIST);

  // Get the size of the group...
  var size = get_group_size();
  // and use it to create groups
  drawGroups(create_groups(size.num_groups));

  // Show the submit button once we've made at least one group
  $("#submit_groups").show();
});

// When either of the size buttons is pressed, make them act like a radio button,
// i.e. only one pressed at a time, and pressing one un-sets the other
$(".group_size_by_btn").click(function() {
  // remove the "active" class from both
  $(".group_size_by_btn").each(function() {$(this).removeClass("active"); });

  // and only add it back to the one which was pressed
  $(this).addClass("active");

  // update the button text
  set_button_text();
});

// regarding .change() only firing on loss of focus
// see https://gist.github.com/brandonaaskov/1596867
// update the button text every time the user changes the value of the number input
$("#number").bind('input', function() {
  set_button_text();
});

// When the "Submit" button is pressed at the bottom of the page.
// Pulls the group data into a js object and passes it to the server via ajax
$("#submit_groups").click(function() {
  // roll through the divs and get each group in turn.
  var groups = [];
  $.each($("#group_div_container").children(".group-container"), function(index, div) {
    var group = {};
    group.name = $(div).find(".group_name").text();
    // get the student ids from the lists in this div and save it as an array in the object
    group.ids = $.map($(div).find("input"), function(item) { return $(item).val(); });
    groups.push(group);
  });

  // pass the current settings here so we can keep them
  var settings = {};
  $(".group_size_by_btn").each(function() {
    if ($(this).hasClass("active")) {
      settings.group_by = $(this).val();
    }
  });
  settings.number = $("#number").val();

  // send the groups object as a post request
  $.ajax({
    type: "POST",
    url: addSession("index.php"),
    data: JSON.stringify({
      "groups":groups,
      "settings":settings
    }),
    contentType: "application/json",
    success: function(response) {
      // redirect to index on success
      window.location.href = addSession("index.php");
    }
  }).done(function() {
    console.log("Group submitted");
  });
});

// Do the work of converting the groups object into html via templates
function drawGroups(groups) {
  // If there's no info here, just hide the submit button for now and bail
  // Happens if there is no saved group information
  if (groups == null) {
    $("#submit_groups").hide();
    return;
  }

  // If we got this far, un-hide the submit button and clear out the div
  $("#submit_groups").show();
  $("#group_div_container").empty();

  // append a template of each group to the div
  for (var i = 0; i < groups.length; i++) {
    context = {};
    context.students = get_student_names_by_id(groups[i].ids);
    context.group = true;
    context.name = groups[i].name;
    $('#group_div_container').append(tsugiHandlebarsRender('list', context));
  }

  // Apply sortable to all of the lists
  $( ".connectedSortable" ).sortable({
    connectWith: ".connectedSortable",
    cancel: ".empty-list-item",
    // event fires when an item is moved into another list
    over: function(event, ui) {
      // get the sender if this was the last item in the list
      if (ui.sender != null && ui.sender.children("li").length == 1) {
        // and add an empty placeholder list item
        ui.sender.append('<li class="list-group-item empty-list-item">Empty</li>');
      }
    },
    // event fires when an item is dropped into another list
    receive: function(event, ui) {
      // remove any "empty" placeholders that were in the list
      ui.item.siblings(".empty-list-item").remove();
    }
  }).disableSelection();
}

// Called when a student is viewing the page.
// Instead of the group & draggables, shows each member of the student's group
// in it's own container that gets styled seperatly than the groups
function drawStudentGroup(group) {
  // groups are in array form for the instructor's screen.
  group = group[0];

  // Set the title for the student group
  $("#student-group-title").text("You are in " + group['name']);

  // Clear the container...
  $("#group_div_container").empty();

  // Add each member in turn..
  for (var i = 0; i < group['members'].length; i++) {
    $('#group_div_container').append(tsugiHandlebarsRender('group_member', group['members'][i]));
  }

  // Put everyone's emails together in the group email link
  var group_emails = [];
  $.each(group['members'], function(index, member) {
    group_emails.push(member['email'])
  });

  $("#email-all").attr("href", "mailto:" + group_emails.join(","));

}

// To avoid storing more data than we have to in the json field, we're just keeping a
// list of ints that match to user_ids. Here is were we match the student ID to a name
// from the STUDENT_LIST variable we load at the beginning.
function get_student_names_by_id(ids) {
  var students = [];
  $.each(ids, function(index, id) {
    for (var i = 0; i < STUDENT_LIST.length; i++) {
      if (STUDENT_LIST[i]["user_id"] == id) {
        students.push(STUDENT_LIST[i]);
        break;
      }
    }
  })
  return students;
}

// Set the text of the button to reflect the current group number/size
function set_button_text() {
  if ($("#create_groups").length) {
    size = get_group_size();
    $("#create_groups").text("Create " + size.num_groups + " Groups of (no more than) " + size.size_groups);
  }
}

// Determine the size of the group/number of groups
function get_group_size() {
  // find out which of the buttons is currently active
  var active_btn;
  $(".group_size_by_btn").each(function() {
    if ($(this).hasClass("active")) {
      active_btn = $(this);
    }
  });

  // divide the length of the student list into the number in the number input
  // will give us either the number of groups or size of groups, based on the active button
  var size = {};
  var num_value = $("#number").val();
  if (active_btn.val() == "groups_by_size") {
    size.num_groups = Math.ceil(STUDENT_LIST.length/num_value);
    size.size_groups = num_value;
  } else {
    size.num_groups = num_value;
    size.size_groups = Math.ceil(STUDENT_LIST.length/num_value);
  }

  return size;
}

// FROM https://bost.ocks.org/mike/shuffle/
function shuffle(array) {
  var m = array.length, t, i;
  // While there remain elements to shuffle…
  while (m) {
    // Pick a remaining element…
    i = Math.floor(Math.random() * m--);

    // And swap it with the current element.
    t = array[m];
    array[m] = array[i];
    array[i] = t;
  }
  return array;
}

// Create "num_groups" of groups by just grabbing them from the student list.
function create_groups(num_groups) {
  // create an array of size "num_groups"
  groups = [];
  for (var i = 0; i < num_groups; i++) {
    groups[i] = {
      name: "Group " + String(i+1),
      ids: []
    };
  }

  // run through the student list and dole out each student to a group in turn
  for (var i = 0; i < STUDENT_LIST.length; i++) {
    groups[i%num_groups].ids.push(STUDENT_LIST[i]["user_id"]);
  }
  return groups;
}
