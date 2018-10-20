# Files replicator for OctoberCMS
OctoberCMS FilesReplicator trait used when replicating any model with files.

## How to use
1. Run `composer require "mariusz-buk/files-replicator"`
2. Define model's properties `$attachOne` and/or `$attachMany`.
3. Add `use FilesReplicator\FilesReplicatorTrait;` to your model class.
4. For replicating model use `$newModel = $oldModel->replicate()`. That's all.
5. If you want update existing model with files from other model (of the same class) use `$myModel->replicateFilesFrom($otherModel)`.

## About this code
I found it would be useful to replicate files when we replicate model. I saw more coders looked for solution so here it is. I hope you'll find it easy to use.

Any questions/suggestions are welcome.

I provide commercial support for your projects.