
document.addEventListener('DOMContentLoaded', function() {
    var selectAllButton = document.getElementById('selectAllButton');
    var checkboxes = document.querySelectorAll('input[type="checkbox"][name="node[]"]');

    selectAllButton.addEventListener('click', function() {
        var isAnyUnchecked = Array.from(checkboxes).some(checkbox => !checkbox.checked && !checkbox.disabled);
        
        checkboxes.forEach(function(checkbox) {
            // 仅当checkbox未被禁用（意味着userLevel >= nodeLevel）时才改变其选中状态
            if (!checkbox.disabled) {
                checkbox.checked = isAnyUnchecked;
            }
        });
    });
});




document.querySelectorAll('.button').forEach(button => {
    button.addEventListener('mousedown', () => {
        button.classList.add('pressed');
    });
    
    button.addEventListener('mouseup', () => {
        setTimeout(() => button.classList.remove('pressed'), 200);
    });

    button.addEventListener('mouseleave', () => {
        setTimeout(() => button.classList.remove('pressed'), 200);
    });
});

document.getElementById('backButton').addEventListener('click', () => {
    window.location.href = '/welcome';
});

document.getElementById('convertButton').addEventListener('click', function () {
    
    var selectedNodes = document.querySelectorAll('input[name="node[]"]:checked');
    var nodeInfos = [];
    selectedNodes.forEach(function (checkbox) {
        var info = encodeURIComponent(checkbox.dataset.fullinfo);
        nodeInfos.push(info);
    });
    
    // 播放音效
    var sound = document.getElementById('buttonSound');
    sound.play();
    
    if (nodeInfos.length > 0) {
        showModalAndProgress(); // 显示模态框和初始化进度条
        var urls = nodeInfos.join('|');
        updateProgress(); // 注意：先启动进度条，再发起请求

        // 获取用户选择的 API 接口
        var apiSelect = document.getElementById('apiSelect').value;

        // 发起异步请求，包括用户选择的 API 接口
        fetch(`node.php?action=convertNodes&urls=${urls}&apiSelect=${apiSelect}`)
            .then(response => response.text())
            .then(text => {
                setTimeout(() => {
                    closeModalAndShowMessage(text); // 显示自定义消息
                }, 1000); // 给进度条动画留出时间
            })
            .catch(error => {
                console.error('Error:', error);
                closeModalAndShowMessage("处理出错，请重试。");
            });
    } else {
        alert('请至少选择一个节点');
    }
});


// 保留“复制”功能
document.getElementById('copyButton').addEventListener('click', function () {
    var selectedNodes = document.querySelectorAll('input[name="node[]"]:checked');
    var nodeInfo = [];
    selectedNodes.forEach(function (checkbox) {
        var info = checkbox.dataset.fullinfo;
        nodeInfo.push(info);
    });

    if (nodeInfo.length > 0) {
        var textToCopy = nodeInfo.join('\n');
        navigator.clipboard.writeText(textToCopy).then(function () {
            alert('节点信息已复制到剪切板');
        }, function (err) {
            alert('复制失败');
        });
    } else {
        alert('请至少选择一个节点');
    }
});

function showModalAndProgress() {
    var modal = document.getElementById('myModal');
    modal.style.display = "block";

    let baseText = "Building";
    let dots = "";
    let progressText = document.getElementById('progressText');
    progressText.textContent = "请稍候，正在处理您的请求..."; // 初始化文本

    // 设置定时器，每100毫秒更新一次文本
    var interval = setInterval(function() {
        dots += ".";
        if (dots.length > 10) { // 如果点的数量超过10，就重置为一个点
            dots = ".";
        }
        progressText.textContent = `${baseText}${dots}`; // 只更新这部分文本
    }, 100);

    // 将定时器ID存储在modal上，以便稍后清除
    modal.setAttribute('data-interval-id', interval.toString());
}



function updateProgress() {
    var progressBar = document.getElementById('myBar');
    var width = 1; // 初始宽度
    var interval = setInterval(frame, 45); // 每50毫秒调用一次frame函数
    
    function frame() {
        if (width >= 100) {
            clearInterval(interval); // 如果进度达到100%，停止定时器
        } else {
            width++; // 否则增加进度
            progressBar.style.width = width + '%'; // 更新进度条宽度
        }
    }
}

// 在合适的时机调用updateProgress，例如在发起异步请求之前


function closeModalAndShowMessage(message) {
    var modal = document.getElementById('myModal');
    var close = modal.querySelector('.close');
    
    // 清除定时器
    var intervalId = parseInt(modal.getAttribute('data-interval-id'), 10);
    clearInterval(intervalId);

    // 设置最终消息
    modal.querySelector('.modal-body p').textContent = message;

    close.onclick = () => {
        modal.style.display = "none";
    };

    // 点击模态框外部也可以关闭
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
}
