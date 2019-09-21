layui.use(['laytpl', 'form', 'laydate', 'element'], function () {
    laytpl = layui.laytpl;
    form = layui.form;
});

//属性编辑调用（并且显示编辑弹框）
function editLayer(attribute_id, title, width, height) {

    $.ajax({
        type : "post",
        url : nc.url("sns://sitehome/info/getAttributeDetails"),
        dataType : "JSON",
        data : {
            'attribute_id' : attribute_id
        },
        success : function(res) {

            var tpl_data = JSON.parse(res);

            laytpl($("#editInfo").html()).render(tpl_data, function (html) {
                index = layer.open({
                    type: 1,
                    title: title,
                    skin: 'layer-tips-class',
                    area: [width, height],
                    content: html,
                });

                form.render();
                /*try {
                    var funcName = 'editInfoLayerAfter';
                    if (typeof(eval(funcName)) == "function") {
                        eval(funcName + '(tpl_data)');
                    }
                } catch (e) {
                }*/
            });
        }
    });
}

//属性添加调用（并且显示添加弹框）
function addLayer(attribute_id, title, width, height) {

    var tpl_data = '{}';

    laytpl($("#addInfo").html()).render(tpl_data, function (html) {
        index = layer.open({
            type: 1,
            title: title,
            skin: 'layer-tips-class',
            area: [width, height],
            content: html,
        });

        form.render();
    });
}

//搜索启用禁用
layui.use(['form', 'laytpl', 'element'], function(){
    var form = layui.form;
    form.on('switch(screening_show)', function(data){

        var attribute_id = data.value;
        var screening_show = data.elem.checked ? 1 : 0;

        $.ajax({
            type : "post",
            url : nc.url("sns://sitehome/info/isCategoryAttribute"),
            dataType : "JSON",
            data : {
                'screening_show' : screening_show,
                'attribute_id' : attribute_id
            },
            success : function(res) {
            }
        });
        return false;
    });
});

//排序更新
function updataSort(attribute_id,_this){

    var sort = $(_this).val();

    // console.log(sort);return;

    $.ajax({
        type: "post",
        url: nc.url("sns://sitehome/info/isCategoryupdateSort"),
        data: {
            'attribute_id' : attribute_id,
            'sort': sort,
        },
        dataType: "JSON",
        success: function (res) {
        }
    });
}

//删除
function del(attributeId){
    layer.confirm('确定删除吗?', {
        btn: ['确定', '取消']
    }, function () {
        $.ajax({
            type: "post",
            url: nc.url("sns://sitehome/info/delCategoryAttribute"),
            data: {
                'attributeId': attributeId,
            },
            dataType: "JSON",
            success: function (res) {
                layer.msg(res.message);
                location.reload();
            }
        });
    }, function () {
        layer.close();
    });
}