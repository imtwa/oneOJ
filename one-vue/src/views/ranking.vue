<template>
    <div class="proble-page">
        <div class="problem-left">
            <div class="pro-card">
                <el-skeleton v-if="loadingSkeleton" :loading="loadingSkeleton" :rows="position" animated />
                <el-table v-else ref="filterTable" :data="tableData">
                    <el-table-column prop="ranking" label="排名" width="80">
                    </el-table-column>
                    <!-- <el-table-column prop="grade" label="等级" width="80">
                        <template slot-scope="scope">
                            <button type="button" class="btn btn-xs"
                                    :style="{ backgroundColor: getBackgroundColor(scope.row.grade) }">
                                    P{{ scope.row.grade }}</button>
                        </template>
                    </el-table-column> -->
                    <el-table-column prop="name" label="昵称" width="120">
                        <template slot-scope="scope">
                            <div style="display: flex;">
                                <!-- <button type="button" class="btn btn-xs">P{{ scope.row.grade }}</button> -->
                                <a :href="'#/UserHome/' + scope.row.id" target="_blank" class="pro-a">
                                    {{ scope.row.name }}
                                </a>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="sign" label="签名">
                        <template slot-scope="scope">
                            <div style=" white-space: nowrap;  
                            overflow: hidden;  ">{{scope.row.sign}}</div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="submit_all" label="提交" width="80">

                    </el-table-column>
                    <el-table-column prop="submit_ac" label="通过" width="80">

                    </el-table-column>
                    <el-table-column prop="submit_pro" label="解决" width="80">

                    </el-table-column>
                    <el-table-column prop="exp" label="经验" width="80">

                    </el-table-column>
                </el-table>
            </div>
            <div class="pro-card">
                <el-pagination background layout="prev, pager, next" :page-count="total" @current-change="currentChange">
                </el-pagination>
            </div>

        </div>
        <div class="problem-right">
            <!-- <div class="pro-card" style="margin-left: 10%;width: 60%;">

            </div> -->
        </div>
    </div>
</template>

<script>

export default {
    name: "RankingComponent",
    data() {
        return {
            tableData: [],
            totalCount: 0,
            page: 0,
            position: 20,
            loadingSkeleton: true,
            total: 0,
            percentage: 0,
            userAc: 0,
        }
    },
    mounted() {
        this.getList();
    },
    comments: {

    },
    methods: {
        getBackgroundColor(i) {
            const colorS = [
                "#cccccc", // 白色 
                "#ADD8E6", // 淡蓝色
                "#00BFFF", // 深天蓝色  
                "#90EE90", // 淡绿色 
                "#00FF7F", // 亮绿色   
                "#FFA500", // 橙色 
                "#8B00FF", // 紫色  
                "#800080", // 紫罗兰色
            ];

            return colorS[i - 1];
        },
        getList() {
            this.loadingSkeleton = true;
            let url = `/user_ranking.php?page=${this.page}&position=${this.position}`;

            this.$axios.get(url)
                .then(response => {
                    // 请求成功时的回调函数

                    this.loadingSkeleton = false;
                    let res = response.data;
                    console.log(res);
                    this.tableData = res.user_ranking;
                    this.total = Math.floor(res.count / this.position) + 1;

                    this.totalCount = res.count;

                })
                .catch(error => {
                    // 请求失败时的回调函数    
                    console.error(error);
                    this.loadingSkeleton = false;
                });
        },
        currentChange(e) {
            this.page = e - 1;
            this.getList();
        }
    }
}
</script>

<style scoped>
.btn-xs {
    padding:2px;
    font-size: 12px;
    line-height: 1;
    border-radius: 4px;
    margin-right: 4px;
}

.btn {
    display: inline-block;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
}

.proble-page {
    width: 100%;
    display: flex;
    flex-direction: row;
    margin-top: 8px;
}

.problem-left {
    width: 70%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.problem-right {
    width: 30%;
    height: 100%;
}

.pro-card {
    width: 70%;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(26, 26, 26, .3);
    margin-top: 16px;
    padding: 8px;
}

.pro-a {
    color: #3498db;
    /* 浅蓝色 */
    text-decoration: none;
}

.pro-a:hover {
    color: #0d4971;
    /* 深蓝色 */
}
</style>