// When the button to create groups is pressed...
$("#create_groups").click(function() {
  // Empty the contents of the current div
  $('#group_div_container').empty();

  // Shuffle the student list
  shuffle(STUDENT_LIST);

  // Get the size of the group...
  var size = get_group_size();
  // and use it to create groups
  var groups = create_groups(size.num_groups);

  // append a template of each group to the div
  for (var i = 0; i < groups.length; i++) {
    context = {};
    context.students = groups[i];
    context.group = true;
    context.name = "Group " + String(i+1);
    $('#group_div_container').append(tsugiHandlebarsRender('list', context));
  }

  // Apply sortable to all of the lists
  $( ".connectedSortable" ).sortable({
    connectWith: ".connectedSortable",
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


});

// when either of the size buttons is pressed
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

// Set the text of the button to reflect the current group number/size
function set_button_text() {
  size = get_group_size();
  $("#create_groups").text("Create " + size.num_groups + " Groups of (no more than) " + size.size_groups);
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
    // initialize each of those to an empty array
    groups[i] = [];
  }

  // run through the student list and dole out each student to a group in turn
  for (var i = 0; i < STUDENT_LIST.length; i++) {
    groups[i%num_groups].push(STUDENT_LIST[i]);
  }
  return groups;
}
