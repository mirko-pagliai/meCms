
var ApiGen = ApiGen || {};
ApiGen.elements = [["c","Album"],["m","Album::albumIsWriteable()"],["m","Album::createAlbum()"],["m","Album::deleteAlbum()"],["m","Album::deletePhoto()"],["m","Album::getAlbumPath()"],["m","Album::getPhotoPath()"],["m","Album::getTmp()"],["m","Album::getTmpPath()"],["m","Album::savePhoto()"],["c","AuthHelper"],["m","AuthHelper::__call()"],["c","Banner"],["p","Banner::$belongsTo"],["p","Banner::$validate"],["m","Banner::afterFind()"],["m","Banner::afterSave()"],["m","Banner::beforeDelete()"],["m","Banner::beforeSave()"],["c","BannerManager"],["m","BannerManager::delete()"],["m","BannerManager::folderIsWritable()"],["m","BannerManager::getFolder()"],["m","BannerManager::getFolderUrl()"],["m","BannerManager::getPath()"],["m","BannerManager::getTmp()"],["m","BannerManager::getTmpPath()"],["m","BannerManager::getUrl()"],["m","BannerManager::save()"],["c","BannersController"],["m","BannersController::admin_add()"],["m","BannersController::admin_delete()"],["m","BannersController::admin_edit()"],["m","BannersController::admin_index()"],["m","BannersController::admin_view()"],["m","BannersController::isAuthorized()"],["c","BannersPosition"],["p","BannersPosition::$displayField"],["p","BannersPosition::$hasMany"],["p","BannersPosition::$validate"],["m","BannersPosition::afterSave()"],["c","BannersPositionsController"],["m","BannersPositionsController::admin_add()"],["m","BannersPositionsController::admin_delete()"],["m","BannersPositionsController::admin_edit()"],["m","BannersPositionsController::admin_index()"],["m","BannersPositionsController::isAuthorized()"],["c","MeAuthComponent"],["p","MeAuthComponent::$action"],["p","MeAuthComponent::$user"],["m","MeAuthComponent::hasId()"],["m","MeAuthComponent::initialize()"],["m","MeAuthComponent::isAction()"],["m","MeAuthComponent::isAdmin()"],["m","MeAuthComponent::isFounder()"],["m","MeAuthComponent::isLogged()"],["m","MeAuthComponent::isManager()"],["c","MeCmsAppController"],["p","MeCmsAppController::$components"],["p","MeCmsAppController::$config"],["p","MeCmsAppController::$helpers"],["m","MeCmsAppController::_getConfig()"],["m","MeCmsAppController::beforeFilter()"],["m","MeCmsAppController::beforeRender()"],["m","MeCmsAppController::isAdminRequest()"],["m","MeCmsAppController::isAuthorized()"],["c","MeCmsAppModel"],["p","MeCmsAppModel::$findMethods"],["m","MeCmsAppModel::_findActive()"],["m","MeCmsAppModel::_findRandom()"],["c","MenuHelper"],["p","MenuHelper::$helpers"],["m","MenuHelper::_banners()"],["m","MenuHelper::_pages()"],["m","MenuHelper::_photos()"],["m","MenuHelper::_posts()"],["m","MenuHelper::_systems()"],["m","MenuHelper::_users()"],["m","MenuHelper::get()"],["c","Page"],["p","Page::$displayField"],["p","Page::$order"],["p","Page::$validate"],["m","Page::afterSave()"],["m","Page::beforeSave()"],["c","PagesController"],["m","PagesController::admin_add()"],["m","PagesController::admin_delete()"],["m","PagesController::admin_edit()"],["m","PagesController::admin_index()"],["m","PagesController::admin_index_statics()"],["m","PagesController::index()"],["m","PagesController::isAuthorized()"],["m","PagesController::request_list()"],["m","PagesController::view()"],["c","Photo"],["p","Photo::$belongsTo"],["p","Photo::$displayField"],["p","Photo::$validate"],["m","Photo::afterFind()"],["m","Photo::afterSave()"],["m","Photo::beforeDelete()"],["m","Photo::beforeSave()"],["c","PhotosAlbum"],["p","PhotosAlbum::$displayField"],["p","PhotosAlbum::$hasMany"],["p","PhotosAlbum::$validate"],["m","PhotosAlbum::_findActive()"],["m","PhotosAlbum::afterSave()"],["m","PhotosAlbum::beforeDelete()"],["m","PhotosAlbum::beforeSave()"],["c","PhotosAlbumsController"],["m","PhotosAlbumsController::admin_add()"],["m","PhotosAlbumsController::admin_delete()"],["m","PhotosAlbumsController::admin_edit()"],["m","PhotosAlbumsController::admin_index()"],["m","PhotosAlbumsController::index()"],["m","PhotosAlbumsController::isAuthorized()"],["m","PhotosAlbumsController::view()"],["c","PhotosController"],["m","PhotosController::admin_add()"],["m","PhotosController::admin_delete()"],["m","PhotosController::admin_edit()"],["m","PhotosController::admin_index()"],["m","PhotosController::isAuthorized()"],["m","PhotosController::request_random()"],["m","PhotosController::view()"],["c","Post"],["p","Post::$belongsTo"],["p","Post::$displayField"],["p","Post::$order"],["p","Post::$validate"],["m","Post::afterSave()"],["m","Post::beforeSave()"],["m","Post::isOwnedBy()"],["c","PostsCategoriesController"],["m","PostsCategoriesController::admin_add()"],["m","PostsCategoriesController::admin_delete()"],["m","PostsCategoriesController::admin_edit()"],["m","PostsCategoriesController::admin_index()"],["m","PostsCategoriesController::index()"],["m","PostsCategoriesController::request_list()"],["c","PostsCategory"],["p","PostsCategory::$actsAs"],["p","PostsCategory::$belongsTo"],["p","PostsCategory::$displayField"],["p","PostsCategory::$hasMany"],["p","PostsCategory::$order"],["p","PostsCategory::$validate"],["m","PostsCategory::_findActive()"],["m","PostsCategory::afterSave()"],["c","PostsController"],["p","PostsController::$components"],["m","PostsController::admin_add()"],["m","PostsController::admin_delete()"],["m","PostsController::admin_edit()"],["m","PostsController::admin_index()"],["m","PostsController::index()"],["m","PostsController::isAuthorized()"],["m","PostsController::request_latest()"],["m","PostsController::request_latest_list()"],["m","PostsController::search()"],["m","PostsController::view()"],["c","StaticPage"],["m","StaticPage::exists()"],["m","StaticPage::getAll()"],["m","StaticPage::getPath()"],["c","SystemsController"],["m","SystemsController::_getVersion()"],["m","SystemsController::admin_cache()"],["m","SystemsController::admin_checkup()"],["m","SystemsController::admin_clear_cache()"],["m","SystemsController::admin_clear_thumbs()"],["m","SystemsController::isAuthorized()"],["c","User"],["p","User::$belongsTo"],["p","User::$displayField"],["p","User::$hasMany"],["p","User::$order"],["p","User::$validate"],["p","User::$virtualFields"],["m","User::beforeSave()"],["m","User::beforeValidate()"],["m","User::oldPasswordIsRight()"],["m","User::passwordsMatch()"],["c","UsersController"],["m","UsersController::_logout()"],["m","UsersController::admin_add()"],["m","UsersController::admin_change_password()"],["m","UsersController::admin_delete()"],["m","UsersController::admin_edit()"],["m","UsersController::admin_index()"],["m","UsersController::admin_view()"],["m","UsersController::isAuthorized()"],["m","UsersController::login()"],["m","UsersController::logout()"],["c","UsersGroup"],["p","UsersGroup::$displayField"],["p","UsersGroup::$hasMany"],["p","UsersGroup::$order"],["p","UsersGroup::$validate"],["c","UsersGroupsController"],["m","UsersGroupsController::admin_add()"],["m","UsersGroupsController::admin_delete()"],["m","UsersGroupsController::admin_edit()"],["m","UsersGroupsController::admin_index()"],["m","UsersGroupsController::isAuthorized()"]];
