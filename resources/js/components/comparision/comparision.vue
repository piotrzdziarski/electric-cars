<template>
    <div class="modal fade" id="comparision" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">

        <progress-bar
                :progressBarCount="progressBarCount"
                :data_retrieved="data_retrieved">
        </progress-bar>

        <div class="modal-dialog modal-dialog-centered" role="document" v-if="!comparing">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <i class="icon-flow-cross"></i>
                        <span class="text-header">Classifieds compare list</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="close-sign" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="error-div" v-if="error">
                    <span class="error-info">
                        <i class="icon-exclamation"></i>You can maximally add only 3 classifieds
                    </span>
                    <button type="button" class="close" aria-label="Close" @click="close_error">
                        <span class="close-sign" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="list-title">
                        You have the following classifieds for comparison:
                    </div>
                    <div class="comparision-list">

                        <transition-group name="slide-fade">
                            <div class="list-item" :key="product.id" v-for="product in products_json">
                                <div class="item-image">
                                    <div class="inner">
                                        <img class="image" src="/images/cars/carvertical.jpg">
                                    </div>
                                </div>
                                <div class="item-title">
                                    {{ product.title }}
                                </div>
                                <div class="item-delete" @click="delete_product(product.id)">
                                    Delete
                                    <i class="icon-minus-circled"></i>
                                </div>
                            </div>
                        </transition-group>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal-btn" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="comparing_method">Compare</button>
                </div>
            </div>
        </div>


        <div class="modal-dialog modal-dialog-centered" role="document" v-if="comparing">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <i class="icon-flow-cross"></i>
                        <span class="text-header">Classifieds compare</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="close-sign" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="error-div" v-if="error">
                    <span class="error-info">
                        <i class="icon-exclamation"></i>You can maximally add only 3 classifieds
                    </span>
                    <button type="button" class="close" aria-label="Close" @click="close_error">
                        <span class="close-sign" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="list-title">
                        You have the following classifieds for comparison:
                    </div>
                    <div class="comparision-list">


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal-btn" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Compare</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "comparision",
        data() {
            return {
                error: false,
                products_json: [],
                data_retrieved: false,
                progressBarCount: 0,
                comparing: false
            }
        },
        methods: {
            compare(id) {
                this.progressBarCount += 1;
                this.data_retrieved = false;
                axios.post('/api/comparision_add', {
                    'id': id
                }).then((Response) => {
                    this.data_retrieved = true;
                    console.log(Response.data);

                    if(Response.data.length > 3) {
                        //remove last item cause it is only info
                        Response.data.splice(-1,1);
                        this.error = true;
                        this.products_json = Response.data;
                    } else {
                        this.error = false;
                    }

                    if (Response.data !== "not_set") {
                        this.products_json = Response.data;
                    }
                }).catch(error => {
                    if (error.response.status === 500) {
                        alert('There was a problem with connecting to our servers! Try again later!');
                    }
                    if (error.response.status === 429) {
                        alert(error.response.statusText + '. Wait 15 seconds and then try again.');
                    }
                });
            },

            delete_product(id) {
                this.progressBarCount += 1;
                this.data_retrieved = false;

                axios.delete('/api/comparision_delete', {
                    data: {'id': id}
                }).then(() => {
                    this.data_retrieved = true;
                    for (let i = 0; i < this.products_json.length; i++) {
                        if (this.products_json[i].id === id) {
                            this.products_json.splice(i, 1);
                        }
                    }
                }).catch(error => {
                    if (error.response.status === 500) {
                        alert('There was a problem with connecting to our servers! Try again later!');
                    }
                    if (error.response.status === 429) {
                        alert(error.response.statusText + '. Wait 15 seconds and then try again.');
                    }
                });
            },

            comparing_method() {
                this.comparing = true;
            },

            close_error() {
                this.error = false;
            }
        }
    }
</script>

<style lang="scss" scoped>

    .slide-fade-enter-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-leave-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-enter, .slide-fade-leave-to {
        transform: translateX(10px);
        opacity: 0;
    }

    .modal-content {
        box-shadow: 0 4px 8px #666;
        padding: 8px;
        border-radius: 5px;
        font-family: "Roboto", sans-serif;

        .text-header {
            font-size: 26px;
            font-weight: 700;
        }

        .icon-flow-cross {
            font-size: 24px;
            padding-right: 5px;
        }

        .modal-header {
            border-bottom: 0;

            .close {
                opacity: 0.90;
                padding: .9rem 22px .9rem .9rem;

                .close-sign {
                    text-align: right;
                    display: block;
                    color: black;
                    font-size: 42px;
                    font-weight: 700;
                }
            }

            .close:hover {
                opacity: 0.8;
            }

            .close:active, .close:focus {
                outline: none;
            }
        }

        .error-div {
            background: #f8e7e7;
            margin: 5px;
            padding: 15px 10px 14px;
            border-radius: 2px;
            color: #d50000;
            display: table;
            position: relative;

            .error-info {
                display: table-cell;
                vertical-align: middle;
                font-size: 16px;
                font-weight: 500;

                .icon-exclamation {
                    font-size: 20px;
                    padding-right: 5px;
                }
            }

            .close {
                opacity: 0.60;
                padding-right: 5px;
                padding-left: 5px;

                .close-sign {
                    height: 100%;
                    display: block;
                    color: black;
                    font-size: 28px;
                    font-weight: 700;
                }
            }

            .close:hover {
                opacity: 0.5;
            }

            .close:active, .close:focus {
                outline: none;
            }

        }

        .modal-body {
            min-height: 1px;
            max-height: 100%;
            padding: 10px 10px 10px 10px;

            .list-title {
                font-size: 18px;
                font-weight: 500;
                padding-bottom: 5px;
                color: #333;
            }

            .comparision-list {

                .list-item {
                    display: flex;
                    padding: 0 5px 10px 5px;
                    position: relative;
                    border-bottom: 1px solid #eaeaea;

                    @media (min-width: 476px) {
                        padding: 10px;
                    }

                    .item-image {
                        width: 25%;

                        .image {
                            height: 100%;
                            width: 100%;
                        }
                    }

                    .item-title {
                        color: #555;
                        width: 60%;
                        padding: 0 10px 10px 10px;
                    }

                    .item-delete {
                        cursor: pointer;
                        position: absolute;
                        color: #444;
                        bottom: 5px;
                        right: 5px;

                        .icon-minus-circled {
                            color: dodgerblue;
                        }

                    }

                    .item-delete:hover {
                        opacity: 0.9;
                    }
                }

                .list-item:last-child {
                    border: 0;
                }
            }
        }

        .modal-footer {
            position: relative;
            border-top: 0;

            .close-modal-btn {
                margin-right: 0;
                bottom: 18px;
                left: 25px;
                position: absolute;
                background: transparent;
                color: #444;
            }

            .close-modal-btn:focus, .close-modal-btn:active {
                box-shadow: none;
            }

            .close-modal-btn:hover {
                color: #222;
                box-shadow: 0 2px 4px #ddd;
            }

            .btn-primary {
                box-shadow: none;
            }
        }

    }
</style>