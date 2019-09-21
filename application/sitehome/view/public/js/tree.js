var tree_id_name = "tree_box";


//权限列表目标标记
var tree_target = [];
var form;
//背景颜色配置
var bg_arr = ['#ededed','#f3f3f3','#f7f7f7','#fbfbfb'];
layui.use(['form'], function () {
    form = layui.form;


    form.on('checkbox(group_form)', function(data){
        var checked = $(data.elem).prop('checked');
        var class_name = $(data.elem).attr("class");

        var target = $(data.elem).attr('data-target');
        var tag = $(data.elem).attr('data-tag');
        if(checked){
            if($("#"+tree_id_name +" input[value='"+tag+"']").length > 0 && $("#"+tree_id_name +" input[value='"+tag+"']").prop('checked') == false){
                $("#"+tree_id_name +" input[value='"+tag+"']").prop('checked', true);
            }
        }

        target = target.split(',');
        var curr_data = find_data_by_target(tree_data[tag], target);
        curr_data['checked'] = checked;
        down_alter(curr_data['child_list'], checked);
        up_alter(tree_data[tag], target, tag);

        $("#"+tree_id_name+" .group_box_"+tag).html(compile_tree(tree_data[tag], 0, '', tag));
        // console.log(data.elem); //得到checkbox原始DOM对象
        // console.log(data.elem.checked); //是否被选中，true或者false
        // console.log(data.value); //复选框value值，也可以通过data.elem.value得到
        // console.log(data.othis); //得到美化后的DOM对象
        form.render();

    });

    form.on('checkbox(addon_form)', function(data){
        var checked = $(data.elem).prop('checked');
        var tag = $(data.elem).val();
        down_alter(tree_data[tag], checked);
        $("#"+tree_id_name+" .group_box_"+tag).html(compile_tree(tree_data[tag], 0, '', tag));
        form.render();
        event.stopPropagation();
    })

    //绑定点击事件
    // $(".tree-box").on('click', '.menu-cell .group-checkbox', function(){
    //
    //     var checked = $(this).prop('checked');
    //     var target = $(this).attr('data-target');
    //     var tag = $(this).attr('data-tag');
    //     target = target.split(',');
    //     var curr_data = find_data_by_target(tree_data[tag], target);
    //
    //     curr_data['checked'] = checked;
    //     down_alter(curr_data['child_list'], checked);
    //     up_alter(tree_data[tag], target, tag);
    //
    //     $("#"+tree_id_name+" .group_box_"+tag).html(compile_tree(tree_data[tag], 0, '', tag));
    // })
    eachTree();
    form.render();






})

function eachTree(){
    //遍历数组
    $.each(tree_data, function(name, val) {
        $(".group_box_"+name).html(compile_tree(val, 0, '', name));
    });
    form.render();
}

//编译树
function compile_tree(data, level, html, tag){
    var num = 0;
    var child_num_arr = [];
    var margin_left = level * 40;
    var start_div = '<div style="margin-left:'+ margin_left +'px;background-color:'+ bg_arr[level] +';" class="tree-line">';
    html += start_div;
    for(var per in data){
        var child_num = data[per]['child_num'];
        child_num_arr.push(child_num);
        var name = data[per]['name'];
        tree_target[level] = name;
        tree_target = tree_target.slice(0, level+1);
        var checked = data[per]['checked'];
        var checked_html = checked ? 'checked' : '';
        var checked_class = checked ? 'layui-form-checked' : '' ;
        if(num > 0){
            if(child_num == 0 && child_num_arr[num-1] == 0) html = html.replace(/<\/div>$/, '');
            else html += start_div;
        }
        var class_string = 'class="group-checkbox"';
        if(name == "ADDON_AUTH" || name == "ADDON_DIYVIEW" ){
            class_string = '';
        }
        html += '<input '+class_string+' lay-filter="group_form" type="checkbox" '+ checked_html +' lay-skin="primary" title="'+data[per]['title']+'" '+ checked_html +' data-tag="'+tag+'" data-target="'+ tree_target.toString() +'" value="'+ name +'"/>';

        html += '</div>';
        if(child_num > 0) html += compile_tree(data[per]['child_list'], level+1, '', tag);
        num ++;
    }
    return html;
}

//通过标记找到数据位置
function find_data_by_target(list, target){
    var data = list;
    for(var per in target){
        if(per == 0){
            data = data[target[per]];
        }else{

            data = data['child_list'][target[per]];
        }
    }
    return data;
}

//向上遍历
function up_alter(list, target, tag){

    target = target.slice(0, target.length - 1);
    if(target.length == 0) return;

    var curr_data = find_data_by_target(tree_data[tag], target);

    var count = 0;
    for(var per in curr_data['child_list']){
        if(curr_data['child_list'][per]['checked']){
            count++;
            break;
        }
    }
    if(count == 0){
        curr_data['checked'] = false;
    }else{
        curr_data['checked'] = true;
    }

    up_alter(list, target, tag);
}

//向下遍历
function down_alter(list, checked){
    for(var per in list){
        list[per]['checked'] = checked;
        if(list[per]['child_num'] > 0) down_alter(list[per]['child_list'], checked)
    }
}
//渲染
function treeRender(){
    //遍历数组
    $.each(tree_data, function(name, val) {
        $("#"+tree_id_name+" .group_box_"+name).html(compile_tree(val, 0, '', name));
    });
    form.render();
}
/**
 * 选中树中的复选框
 */
function selectedTree(temp_data, selected_arr){
    var return_data = {};
    if(!$.isEmptyObject(temp_data)){
        $.each(temp_data, function(name, val) {
            if(selected_arr.indexOf(val['name']) != -1){
                val['checked'] = true;
                if(val['child_num'] > 0) {
                    val['child_list'] = selectedTree(val['child_list'],selected_arr)
                }
            }
            return_data[name] = val;
        });
    }
    return return_data;

}




