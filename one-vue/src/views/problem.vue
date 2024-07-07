<template>
    <div class="proble-page">
        <div class="problem-left">
            <div class="pro-card">
                <el-skeleton v-if="loadingSkeleton" :loading="loadingSkeleton" :rows="position" animated />
                <el-table v-else ref="filterTable" :data="tableData">
                    <el-table-column prop="state" label="状态" width="80">
                        <template slot-scope="scope">
                            <el-tag :type="scope.row.state === 'AC' ? 'success' : 'danger'"
                                v-if="scope.row.state != ''">
                                {{ scope.row.state }}
                            </el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column prop="id" label="题号" width="80">
                        <template slot-scope="scope">
                            {{ String(scope.row.id).padStart(4, '0') }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="题目">
                        <template slot-scope="scope">
                            <a :href="'#/ProblemInfo/' + scope.row.id" target="_blank" class="pro-a">
                                {{ scope.row.name }}
                            </a>
                        </template>
                    </el-table-column>
                    <el-table-column prop="degree" label="难度" width="80">
                        <template slot-scope="scope">
                            <el-tag
                                :type="scope.row.degree === '简单' ? 'success' : scope.row.degree === '中等' ? 'warning' : 'danger'"
                                effect="dark">
                                {{ scope.row.degree }}
                            </el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column label="通过率" width="80">
                        <template slot-scope="scope">
                            {{ scope.row.ac_count }}/{{ scope.row.submit_count }}
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="pro-card">
                <el-pagination background layout="prev, pager, next" :page-count="total" @current-change="currentChange">
                </el-pagination>
            </div>

        </div>
        <div class="problem-right">
            <div class="pro-card" style="margin-left: 10%;width: 60%;">
                <el-progress type="circle" :percentage="percentage">
                </el-progress>
                <div>
                    当前进度 {{ userAc }}/{{ totalCount }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "ProblemComponent",
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
        this.getUser();
    },
    comments: {

    },
    methods: {
        getUser() {
            if (this.$store.state.userSession.id === null || this.$store.state.userSession.id === undefined) {
                return;
            }
            let url = `/user_submit.php?user_id=${this.$store.state.userSession.id}`;

            this.$axios.get(url)
                .then(response => {
                    // 请求成功时的回调函数    
                    let res = response.data;
                    this.userAc = res.user_data.submit_pro;
                    this.percentage = this.userAc * 100 / res.count;
                    this.percentage = this.percentage.toFixed(2);
                    this.percentage = parseFloat(this.percentage);
                })
                .catch(error => {
                    // 请求失败时的回调函数    
                    console.error(error);
                });
        },
        getList() {
            this.loadingSkeleton = true;
            let url;
            if (this.$store.state.userSession.id === null || this.$store.state.userSession.id === undefined) {
                url = `/problem_list.php?page=${this.page}&position=${this.position}`;
            }else{
                url = `/problem_list.php?page=${this.page}&position=${this.position}&user_id=${this.$store.state.userSession.id}`;
            }

            this.$axios.get(url)
                .then(response => {
                    // 请求成功时的回调函数    
                    let res = response.data;
                    // console.log(res);
                    this.tableData = res.total_data;
                    this.total = Math.floor(res.count / this.position) + 1;

                    // this.userAc = res.total_data.filter(obj => obj.state === 'AC').length
                    this.totalCount = res.count;
                    // this.percentage = this.userAc*100/this.totalCount;
                    // this.percentage = this.percentage.toFixed(2);

                    this.loadingSkeleton = false;
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