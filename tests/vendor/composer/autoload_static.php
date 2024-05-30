<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd129e47fcb8ccfa4857a7e42930b1cc8
{
    public static $classMap = array (
        'APP\\plugins\\generic\\pidManager\\classes\\Db\\PluginDao' => __DIR__ . '/../..' . '/../classes/Db/PluginDao.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Helpers\\ArrayHelper' => __DIR__ . '/../..' . '/../classes/Helpers/ArrayHelper.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Helpers\\ClassHelper' => __DIR__ . '/../..' . '/../classes/Helpers/ClassHelper.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Helpers\\LogHelper' => __DIR__ . '/../..' . '/../classes/Helpers/LogHelper.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Helpers\\StringHelper' => __DIR__ . '/../..' . '/../classes/Helpers/StringHelper.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnArticleView' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnArticleView.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnConstants' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnConstants.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnDao' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnDao.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnDataModel' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnDataModel.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnSchema' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnSchema.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnWorkflowForm' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnWorkflowForm.php',
        'APP\\plugins\\generic\\pidManager\\classes\\Igsn\\IgsnWorkflowTab' => __DIR__ . '/../..' . '/../classes/Igsn/IgsnWorkflowTab.php',
        'APP\\plugins\\generic\\pidManager\\classes\\PID\\AbstractPid' => __DIR__ . '/../..' . '/../classes/PID/AbstractPid.php',
        'APP\\plugins\\generic\\pidManager\\classes\\PID\\Doi' => __DIR__ . '/../..' . '/../classes/PID/Doi.php',
        'APP\\plugins\\generic\\pidManager\\classes\\PID\\Handle' => __DIR__ . '/../..' . '/../classes/PID/Handle.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitd129e47fcb8ccfa4857a7e42930b1cc8::$classMap;

        }, null, ClassLoader::class);
    }
}
