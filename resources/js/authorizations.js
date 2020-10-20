let user = window.App.user;

module.exports = {
    owns (model, prop = 'user_id') {
        return parseInt(model[prop]) === user.id;
    },

    hasPermission (permission) {
        return user.permissions.includes(permission);
    },

    isVerified () {
        return user.isVerified;
    }
};
