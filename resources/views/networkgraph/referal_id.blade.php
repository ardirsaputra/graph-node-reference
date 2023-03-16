<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <style>
      body,html, svg, #container {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        font-family: verdana;
        overflow: hidden;
        width: 100%;
        height: 100%;
      }

      #controls {
        position:absolute;
        top:0;
        left:0px;
        padding:4px;
        border-bottom: 1px solid #ccc;
        width:100%;
        font-size: 0.7em;
      }

      #controls input {
        font-size:14px
      }

      #controls input[type="button"],
      #controls input[type="submit"] {
        font-size:18px
      }
    </style>
    <link rel="stylesheet" href="../src/ggraph.css" />
    <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="../src/marker/marker.js"></script>

    <script src="../ext/simmelian.js"></script>
    <script src="../ext/singles.js"></script>

    <script src="../src/algorithms/ladjust.js"></script>
    <script src="../src/algorithms/makecluster.js"></script>

    <script src="../src/modules/labels.js"></script>
    <script src="../src/modules/groups.js"></script>
    <script src="../src/modules/nodes.js"></script>
    <script src="../src/modules/selection.js"></script>

    <script src="../src/modules/member_lines.js"></script>

    <script src="../src/modify/merger.js"></script>
    <script src="../src/modify/splitter.js"></script>
    <script src="../src/modify/remover.js"></script>

    <script src="../src/ggraph.js"></script>
    <title>Referal Network Graph</title>
  </head>
  <body>
    <div id="controls">
      User Referal - Network Graph
      |
      <input type="radio" name="mode" value="select" checked="checked" id="mode-select" onclick="ggraph.set_mode('select')"> <label for="mode-select">Select</label>
      &nbsp;
      <input type="radio" name="mode" value="drag" id="mode-drag" onclick="ggraph.set_mode('drag')"> <label for="mode-drag">Drag</label>
      |
      Nodes: <span id="node-count">0</span>
      Edges: <span id="edge-count">0</span>
      | <a href="/networkgraph/createdby"><button type="submit" name="swith view" value="drag"><label for="mode-swich">User Created By Network Graph</label></a>
    </div>
    <div id="container"></div>
  </body>
  <script>
    var all;
    var selected;
    var graph;
    var converted;
    var type = null;
    var nodes_data = JSON.parse(JSON.stringify({!! json_encode($nodes) !!}));
    var links_data = JSON.parse(JSON.stringify({!! json_encode($links) !!}));
    console.log(nodes_data);
    console.log(links_data);
    ggraph.init('container', 25);

    var merge = function() {
      ggraph.merge(selection.all());
    }

    var remove = function() {
      var s = [];
      for (var k in selected) {
        s.push({id: k})
      }
      ggraph.remove(s);
    }

    var split = function() {
      ggraph.split(all);
    }

    function _singles(e) {
      var groups = singles.get_groups(converted);
      all = groups;
      ggraph.merge(groups);
    };

    function _backbones(e) {
      var res = simmelian.filter(converted.all_links, 0.75);
      ggraph.filter_links(res);
    };

    function _set_comment() {
      if (selection.all().length === 0) return;
      var comment = document.getElementById('comment').value;
      var mock = ggraph.convert({
        nodes: [{id: comment, type: 'comment'}],
        links: []
      });

      var original = ggraph.get_graph();
      var comment_obj = mock.nodes[0];

      original.nodes.push(comment_obj);

      for (var key in mock.member_map) {
        original.member_map[key] = mock.member_map[key];
      }

      for (var key in mock.group_map) {
        original.group_map[key] = original.nodes.length - 1;
      }

      document.getElementById('comment').value = '';

      original.all_links[comment] = {}

      var tx = 0;
      var ty = 0;

      selection.all().map(function(selected) {
        var group_id = original.member_map[selected.id].group;
        var target = original.nodes[original.group_map[group_id]];
        tx += target.x;
        ty += target.y;

        original.all_links[comment][selected.id] = 1;
        original.links.push({
          source: comment_obj,
          target: target,
          value: 1
        })
      });

      comment_obj.x = tx / selection.all().length;
      comment_obj.y = ty / selection.all().length;

      ggraph.draw(original);

      selection.clear();
    }

    ggraph.on_select(function(_all, _selected) {
      all = selection.all();
      selected = selection.selected();
    });

    var graph = {
      nodes:nodes_data,
      links:links_data
    }

    document.getElementById('node-count').innerHTML = graph.nodes.length;
    document.getElementById('edge-count').innerHTML = graph.links.length;

    converted = ggraph.convert(graph);

    ggraph.draw(converted);
  </script>
</html>