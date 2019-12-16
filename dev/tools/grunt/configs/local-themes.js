// grunt exec:dvcampus_luma_en_US && grunt less:dvcampus_luma_en_US && grunt watch
module.exports = {
    dvcampus_luma_en_US: {
        area: 'frontend',
        name: 'DvCampus/luma',
        locale: 'en_US',
        files: [
            'css/styles-m',
            'css/styles-l'
        ],
        dsl: 'less'
    },
    dvcampus_luma_uk_UA: {
        area: 'frontend',
        name: 'DvCampus/luma',
        locale: 'uk_UA',
        files: [
            'css/styles-m',
            'css/styles-l'
        ],
        dsl: 'less'
    },
    dvcampus_luma_ru_RU: {
        area: 'frontend',
        name: 'DvCampus/luma',
        locale: 'ru_RU',
        files: [
            'css/styles-m',
            'css/styles-l'
        ],
        dsl: 'less'
    }
};
